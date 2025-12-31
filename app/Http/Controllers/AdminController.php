<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

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
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video' => 'required|file|mimes:mp4|max:153600', // 150MB max (in KB)
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

            // Get video duration using FFmpeg or fallback
            $duration = $this->getVideoDuration(storage_path('app/public/' . $videoPath));

            Video::create([
                'title' => $request->title,
                'description' => $request->description,
                'video_path' => $videoPath,
                'duration' => $duration,
            ]);

            // Get the created video for response
            $video = Video::where('video_path', $videoPath)->first();

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully! Duration: ' . gmdate('i:s', $duration),
                'video_id' => $video->id,
                'duration' => $duration
            ]);
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

    public function updateVideoDuration(Request $request)
    {
        try {
            $request->validate([
                'video_id' => 'required|exists:videos,id',
                'duration' => 'required|numeric|min:0',
            ]);

            $video = Video::findOrFail($request->video_id);
            $video->update(['duration' => (int) $request->duration]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duration update failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Duration update failed: ' . $e->getMessage());
        }
    }

    public function manageInterns()
    {
        $interns = User::where('role', 'intern')->get();
        return view('admin.interns', compact('interns'));
    }

    public function reports()
    {
        $reports = VideoProgress::with(['user', 'video'])
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
                    'completion_status' => $progress->is_completed ? 'Completed' : 'In Progress'
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

        return view('admin.reports', compact('reports', 'userWatchCounts'));
    }

    private function getVideoDuration($filePath)
    {
        try {
            // Try to use FFmpeg if available
            if (class_exists('FFMpeg\FFMpeg')) {
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open($filePath);
                $duration = $video->getFormat()->get('duration');
                return (int) $duration;
            }
        } catch (\Exception $e) {
            // FFmpeg not available or failed, use fallback
        }

        // Fallback: Try to get duration using getID3 if available
        try {
            if (class_exists('getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);
                if (isset($fileInfo['playtime_seconds'])) {
                    return (int) $fileInfo['playtime_seconds'];
                }
            }
        } catch (\Exception $e) {
            // getID3 not available
        }

        // Last resort: Return 0 and let JavaScript handle it
        // We'll create a method to update duration via AJAX
        return 0;
    }

    public function diagnostics()
    {
        $phpInfo = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'loaded_config' => php_ini_loaded_file(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        ];

        // Convert to bytes for display
        $phpInfo['upload_max_filesize_bytes'] = $this->convertToBytes($phpInfo['upload_max_filesize']);
        $phpInfo['post_max_size_bytes'] = $this->convertToBytes($phpInfo['post_max_size']);
        $phpInfo['memory_limit_bytes'] = $this->convertToBytes($phpInfo['memory_limit']);

        // Check if limits are sufficient
        $phpInfo['upload_ok'] = $phpInfo['upload_max_filesize_bytes'] >= 157286400; // 150MB
        $phpInfo['post_ok'] = $phpInfo['post_max_size_bytes'] >= 209715200; // 200MB
        $phpInfo['memory_ok'] = $phpInfo['memory_limit_bytes'] >= 268435456; // 256MB

        return view('admin.diagnostics', compact('phpInfo'));
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
}
