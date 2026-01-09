<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        // Get all videos in the same category ordered by part_number
        $categoryId = $video->category_id;
        $allVideos = Video::where('category_id', $categoryId)
            ->orderBy('part_number', 'asc')
            ->get();

        // Find current video index
        $currentIndex = $allVideos->search(fn($v) => $v->id == $id);

        // Get next video if exists
        $nextVideo = null;
        if ($currentIndex !== false && $currentIndex + 1 < $allVideos->count()) {
            $nextVideo = $allVideos[$currentIndex + 1];
        }

        return view('video.learning', compact('video', 'progress', 'allVideos', 'currentIndex', 'nextVideo'));
    }

    public function saveProgress(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'watched_duration' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        Log::info('SaveProgress called by user ' . $user->id . ' (' . $user->role . ') for video ' . $request->video_id . ', duration: ' . $request->watched_duration);

        $video = Video::findOrFail($request->video_id);

        $progress = VideoProgress::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $request->video_id],
            ['watched_duration' => $request->watched_duration]
        );

        // Check if completed
        $completed = $request->boolean('completed', false);
        $isCompleted = $completed;

        Log::info("SaveProgress: video_id={$request->video_id}, watched_duration={$request->watched_duration}, completed={$completed}, video_duration={$video->duration}");

        if ($video->duration > 0) {
            // Allow 95% completion or within 10 seconds of end
            $isCompleted = $request->watched_duration >= ($video->duration * 0.95) ||
                $request->watched_duration >= ($video->duration - 10);
            Log::info("SaveProgress: calculated isCompleted={$isCompleted}");
        }

        if ($isCompleted && $completed) {
            // Only increment watch count when explicitly marked as completed (forceComplete = true)
            $progress->increment('watch_count');
            $progress->update(['is_completed' => true]);
            Log::info("SaveProgress: marked as completed, watch_count incremented to " . $progress->watch_count);
        } elseif ($isCompleted) {
            // Just mark as completed without incrementing watch count
            $progress->update(['is_completed' => true]);
            Log::info("SaveProgress: marked as completed without incrementing watch count");
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

        Log::info("UpdateDuration: video_id={$request->video_id}, old_duration={$oldDuration}, new_duration={$request->duration}");

        return response()->json(['success' => true]);
    }

    public function markVideoComplete(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
        ]);

        $user = Auth::user();
        $videoId = $request->video_id;

        // Update or create progress record
        $progress = VideoProgress::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $videoId],
            ['is_completed' => true, 'watched_duration' => DB::raw('duration')]
        );

        // Increment watch count
        $progress->increment('watch_count');

        // Get the current video to find next part
        $video = Video::find($videoId);
        $nextVideo = Video::where('category_id', $video->category_id)
            ->where('part_number', '>', $video->part_number)
            ->orderBy('part_number', 'asc')
            ->first();

        return response()->json([
            'success' => true,
            'completed' => true,
            'nextVideo' => $nextVideo ? [
                'id' => $nextVideo->id,
                'title' => $nextVideo->title,
                'url' => route('video.watch', $nextVideo->id)
            ] : null
        ]);
    }
}