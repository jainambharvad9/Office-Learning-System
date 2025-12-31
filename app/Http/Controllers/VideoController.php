<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function watch($id)
    {
        $video = Video::findOrFail($id);
        $user = auth()->user();

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

        $user = auth()->user();
        $video = Video::findOrFail($request->video_id);

        $progress = VideoProgress::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $request->video_id],
            ['watched_duration' => $request->watched_duration]
        );

        // Check if completed (allow for small tolerance)
        if ($video->duration > 0 && $request->watched_duration >= ($video->duration - 5)) { // 5 second tolerance
            $progress->update(['is_completed' => true]);
        }

        return response()->json(['success' => true]);
    }
}
