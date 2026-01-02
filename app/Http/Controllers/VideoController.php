<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function watch($id)
    {
        $video = Video::findOrFail($id);
        $user = Auth::user();

        // Check if user can access this video (for now, all can)
        $progress = VideoProgress::firstOrCreate(
            ['user_id' => $user->id, 'video_id' => $video->id],
            ['watched_duration' => 0, 'is_completed' => false]
        );

        return view('video.learning', compact('video', 'progress'));
    }

    public function saveProgress(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'watched_duration' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        \Log::info('SaveProgress called by user ' . $user->id . ' (' . $user->role . ') for video ' . $request->video_id . ', duration: ' . $request->watched_duration);

        $video = Video::findOrFail($request->video_id);

        $progress = VideoProgress::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $request->video_id],
            ['watched_duration' => $request->watched_duration]
        );

        // Check if completed
        $completed = $request->boolean('completed', false);
        $isCompleted = $completed;

        Log::info("message");
        ("SaveProgress: video_id={$request->video_id}, watched_duration={$request->watched_duration}, completed={$completed}, video_duration={$video->duration}");

        if ($video->duration > 0) {
            // Allow 95% completion or within 10 seconds of end
            $isCompleted = $request->watched_duration >= ($video->duration * 0.95) ||
                $request->watched_duration >= ($video->duration - 10);
            Log::info("SaveProgress: calculated isCompleted={$isCompleted}");
        }

        if ($isCompleted) {
            $progress->increment('watch_count');
            $progress->update(['is_completed' => true]);
            Log::info("SaveProgress: marked as completed, watch_count incremented");
        }

        return response()->json([
            'success' => true,
            'completed' => $progress->is_completed
        ]);
    }

    public function updateDuration(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'duration' => 'required|numeric|min:0',
        ]);

        $video = Video::findOrFail($request->video_id);
        $oldDuration = $video->duration;
        $video->update(['duration' => (int) $request->duration]);

        \Log::info("UpdateDuration: video_id={$request->video_id}, old_duration={$oldDuration}, new_duration={$request->duration}");

        return response()->json(['success' => true]);
    }
}