<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoProgress;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalInterns = User::where('role', 'intern')->count();
        $totalVideos = Video::count();
        $completedVideos = VideoProgress::where('is_completed', true)->count();

        $internProgress = User::where('role', 'intern')
            ->with(['videoProgress.video'])
            ->get()
            ->map(function ($intern) {
                $totalVideos = Video::count();
                $completedVideos = $intern->videoProgress->where('is_completed', true)->count();
                $watchCount = $completedVideos; // Watch count = completed videos
                $progress = $totalVideos > 0 ? ($completedVideos / $totalVideos) * 100 : 0;

                return [
                    'name' => $intern->name,
                    'progress' => round($progress, 1),
                    'status' => $progress == 100 ? 'Completed' : 'In Progress',
                    'watch_count' => $watchCount,
                    'total_videos' => $totalVideos
                ];
            });

        return view('admin.dashboard', compact('totalInterns', 'totalVideos', 'completedVideos', 'internProgress'));
    }

    public function showUploadForm()
    {
        $categories = VideoCategory::active()->get();
        return view('admin.upload', compact('categories'));
    }

    public function uploadVideo(Request $request)
    {
        // Check PHP limits before processing
        $maxUpload = ini_get('upload_max_filesize');
        $maxPost = ini_get('post_max_size');
        $memoryLimit = ini_get('memory_limit');

        // Convert to bytes for comparison
        $maxUploadBytes = $this->convertToBytes($maxUpload);
        $maxPostBytes = $this->convertToBytes($maxPost);

        if ($maxUploadBytes < 157286400 || $maxPostBytes < 209715200) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Server configuration error. Please ensure PHP limits are set correctly:\n" .
                        "upload_max_filesize: {$maxUpload} (should be 150M+)\n" .
                        "post_max_size: {$maxPost} (should be 200M+)\n" .
                        "Use the start_server.bat file to start the server with correct configuration."
                ], 500);
            }
            return redirect()->back()->with(
                'error',
                "Server configuration error. Please ensure PHP limits are set correctly:<br>" .
                    "upload_max_filesize: {$maxUpload} (should be 150M+)<br>" .
                    "post_max_size: {$maxPost} (should be 200M+)<br>" .
                    "Use the start_server.bat file to start the server with correct configuration."
            );
        }

        try {
            // Increase execution time for this specific request
            set_time_limit(600); // 10 minutes

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:video_categories,id',
                'part_number' => 'nullable|integer|min:1',
                'video' => 'required|file|mimes:mp4|max:512000', // 500MB max (in KB)
            ]);

            // Check if file was uploaded successfully
            if (!$request->hasFile('video') || !$request->file('video')->isValid()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload failed. Please try again.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'File upload failed. Please try again.');
            }

            $file = $request->file('video');

            // Additional file size check
            if ($file->getSize() > 157286400) { // 150MB in bytes
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File size exceeds 150MB limit.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'File size exceeds 150MB limit.');
            }

            // Store the video file
            $videoPath = $file->store('videos', 'public');

            if (!$videoPath) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save video file.'
                    ], 500);
                }
                return redirect()->back()->with('error', 'Failed to save video file.');
            }

            // Create video record without duration first (faster)
            $video = Video::create([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'part_number' => $request->part_number ?? 1,
                'video_path' => $videoPath,
                'duration' => 0, // Will be updated asynchronously
            ]);

            $responseData = [
                'success' => true,
                'message' => 'Video uploaded successfully! Duration will be processed shortly.',
                'video_id' => $video->id,
                'duration' => 0
            ];

            Log::info('Upload response:', $responseData);

            // Process duration asynchronously (don't block the response)
            try {
                // Use a job or background process if available, otherwise skip for now
                // This prevents timeout on large files
                if (class_exists('Illuminate\Foundation\Bus\Dispatchable')) {
                    // Could dispatch a job here in the future
                }
            } catch (\Exception $e) {
                Log::warning('Async duration processing failed: ' . $e->getMessage());
            }

            return response()->json($responseData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function processVideoDuration(Video $video)
    {
        try {
            $filePath = storage_path('app/public/' . $video->video_path);
            Log::info('Processing duration for video ' . $video->id . ', path: ' . $filePath);

            if (!file_exists($filePath)) {
                Log::error('Video file not found: ' . $filePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Video file not found'
                ], 404);
            }

            $duration = $this->getVideoDuration($filePath);
            Log::info('Extracted duration: ' . $duration . ' for video ' . $video->id);

            if ($duration > 0) {
                $video->update(['duration' => $duration]);
                Log::info('Updated video ' . $video->id . ' with duration ' . $duration);
            } else {
                Log::warning('Duration is 0 for video ' . $video->id);
            }

            return response()->json([
                'success' => true,
                'duration' => $duration,
                'formatted_duration' => gmdate('i:s', $duration)
            ]);
        } catch (\Exception $e) {
            Log::error('Video duration processing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process video duration: ' . $e->getMessage()
            ], 500);
        }
    }
    public function manageInterns()
    {
        $interns = User::where('role', 'intern')->get();
        return view('admin.interns', compact('interns'));
    }

    public function registerIntern(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'intern',
        ]);

        return redirect()->back()->with('success', 'Intern registered successfully!');
    }

    public function reports()
    {
        $reports = VideoProgress::with(['user', 'video'])
            ->whereHas('user')
            ->whereHas('video')
            ->get()
            ->map(function ($progress) {
                $percentage = $progress->video->duration > 0
                    ? min(100, ($progress->watched_duration / $progress->video->duration) * 100)
                    : 0;

                return [
                    'intern_name' => $progress->user->name,
                    'video_name' => $progress->video->title,
                    'watch_percentage' => round($percentage, 1),
                    'watched_duration' => gmdate('i:s', $progress->watched_duration),
                    'total_duration' => gmdate('i:s', $progress->video->duration),
                    'completion_status' => $progress->is_completed ? 'Completed' : 'In Progress',
                    'watch_count' => $progress->watch_count
                ];
            });

        // Group by user for watch counts
        $userWatchCounts = User::where('role', 'intern')
            ->with(['videoProgress' => function ($query) {
                $query->where('is_completed', true);
            }])
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'watch_count' => $user->videoProgress->count(),
                    'total_videos' => Video::count()
                ];
            });

        // Calculate average completion
        $totalProgress = VideoProgress::count();
        $completedProgress = VideoProgress::where('is_completed', true)->count();
        $averageCompletion = $totalProgress > 0 ? round(($completedProgress / $totalProgress) * 100, 1) : 0;
        return view('admin.reports', compact('reports', 'userWatchCounts', 'averageCompletion'));
    }

    private function getVideoDuration($filePath)
    {
        try {
            // Try to use FFmpeg if available
            if (class_exists('FFMpeg\FFMpeg')) {
                Log::info('Trying FFmpeg for duration extraction');
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open($filePath);
                $duration = $video->getFormat()->get('duration');
                Log::info('FFmpeg extracted duration: ' . $duration);
                return (int) $duration;
            }
        } catch (\Exception $e) {
            Log::info('FFmpeg failed: ' . $e->getMessage());
        }

        // Fallback: Try to get duration using getID3 if available
        try {
            if (class_exists('getID3')) {
                Log::info('Trying getID3 for duration extraction');
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);
                if (isset($fileInfo['playtime_seconds'])) {
                    $duration = (int) $fileInfo['playtime_seconds'];
                    Log::info('getID3 extracted duration: ' . $duration);
                    return $duration;
                } else {
                    Log::warning('getID3 did not find playtime_seconds in file info');
                }
            } else {
                Log::warning('getID3 class not found');
            }
        } catch (\Exception $e) {
            Log::error('getID3 failed: ' . $e->getMessage());
        }

        // Last resort: Return 0
        Log::warning('All duration extraction methods failed, returning 0');
        return 0;
    }

    public function manageVideos()
    {
        $videos = Video::withCount(['progress as completed_count' => function ($query) {
            $query->where('is_completed', true);
        }])->get()->map(function ($video) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'duration' => $video->duration > 0 ? gmdate('i:s', $video->duration) : 'Unknown',
                'file_size' => $this->getFileSize(storage_path('app/public/' . $video->video_path)),
                'upload_date' => $video->created_at->format('M d, Y'),
                'completed_count' => $video->completed_count,
                'total_views' => VideoProgress::where('video_id', $video->id)->count(),
            ];
        });

        return view('admin.videos', compact('videos'));
    }

    public function deleteVideo($id)
    {
        $video = Video::findOrFail($id);

        // Delete the video file from storage
        if (Storage::disk('public')->exists($video->video_path)) {
            Storage::disk('public')->delete($video->video_path);
        }

        // Delete associated progress records
        VideoProgress::where('video_id', $id)->delete();

        // Delete the video record
        $video->delete();

        return redirect()->route('admin.videos')->with('success', 'Video deleted successfully!');
    }

    private function getFileSize($filePath)
    {
        if (file_exists($filePath)) {
            $bytes = filesize($filePath);
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }
        return 'Unknown';
    }

    /**
     * Convert PHP ini size string to bytes
     */
    private function convertToBytes($sizeStr)
    {
        $sizeStr = trim($sizeStr);
        $unit = strtolower(substr($sizeStr, -1));
        $size = (int)substr($sizeStr, 0, -1);

        switch ($unit) {
            case 'g':
                return $size * 1024 * 1024 * 1024;
            case 'm':
                return $size * 1024 * 1024;
            case 'k':
                return $size * 1024;
            default:
                return $size;
        }
    }
    public function deleteIntern($id)
    {
        $intern = User::findOrFail($id);
        if ($intern->role !== 'intern') {
            return redirect()->back()->with('error', 'Cannot delete non-intern user.');
        }
        $intern->delete();
        return redirect()->back()->with('success', 'Intern deleted successfully.');
    }

    // Video Category CRUD Methods
    public function index()
    {
        $categories = VideoCategory::withCount('videos')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:video_categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        VideoCategory::create($request->all());

        return redirect()->route('admin.categories')->with('success', 'Category created successfully.');
    }

    public function edit(VideoCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, VideoCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:video_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $category->update($request->all());

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully.');
    }

    public function destroy(VideoCategory $category)
    {
        if ($category->videos()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with existing videos.');
        }

        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully.');
    }
}
