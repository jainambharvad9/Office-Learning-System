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
                $progress = $totalVideos > 0 ? ($completedVideos / $totalVideos) * 100 : 0;

                return [
                    'name' => $intern->name,
                    'progress' => round($progress, 1),
                    'status' => $progress == 100 ? 'Completed' : 'In Progress'
                ];
            });

        return view('admin.dashboard', compact('totalInterns', 'totalVideos', 'completedVideos', 'internProgress'));
    }

    public function uploadVideo(Request $request)
    {
        try {
            // Check PHP limits before processing
            $maxUpload = ini_get('upload_max_filesize');
            $maxPost = ini_get('post_max_size');
            $memoryLimit = ini_get('memory_limit');

            // Convert to bytes for comparison
            $maxUploadBytes = $this->convertToBytes($maxUpload);
            $maxPostBytes = $this->convertToBytes($maxPost);

            if ($maxUploadBytes < 157286400 || $maxPostBytes < 209715200) { // 150MB and 200MB
                return redirect()->back()->with(
                    'error',
                    "Server configuration error. Please ensure PHP limits are set correctly:<br>" .
                        "upload_max_filesize: {$maxUpload} (should be 150M+)<br>" .
                        "post_max_size: {$maxPost} (should be 200M+)<br>" .
                        "Use the start_server.bat file to start the server with correct configuration."
                );
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video' => 'required|file|mimes:mp4|max:153600', // 150MB max (in KB)
            ]);

            // Check if file was uploaded successfully
            if (!$request->hasFile('video') || !$request->file('video')->isValid()) {
                return redirect()->back()->with('error', 'File upload failed. Please try again.');
            }

            $file = $request->file('video');

            // Additional file size check
            if ($file->getSize() > 157286400) { // 150MB in bytes
                return redirect()->back()->with('error', 'File size exceeds 150MB limit.');
            }

            // Store the video file
            $videoPath = $file->store('videos', 'public');

            if (!$videoPath) {
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
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function updateVideoDuration(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'duration' => 'required|numeric|min:0',
        ]);

        $video = Video::findOrFail($request->video_id);
        $video->update(['duration' => (int) $request->duration]);

        return response()->json(['success' => true]);
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
                    'completion_status' => $progress->is_completed ? 'Completed' : 'In Progress'
                ];
            });

        return view('admin.reports', compact('reports'));
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
