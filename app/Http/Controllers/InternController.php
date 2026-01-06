<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoProgress;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $categoryId = $request->get('category');

        // Get all active categories
        $categories = VideoCategory::active()->get();

        // Filter videos by category if selected
        $videosQuery = Video::query();
        if ($categoryId) {
            $videosQuery->where('category_id', $categoryId);
        }

        $videos = $videosQuery->get()->map(function ($video) use ($user) {
            $progress = VideoProgress::where('user_id', $user->id)
                ->where('video_id', $video->id)
                ->first();

            $status = 'Pending';
            $progressPercent = 0;

            if ($progress) {
                if ($progress->is_completed) {
                    $status = 'Completed';
                    $progressPercent = 100;
                } else {
                    $status = 'In Progress';
                    $progressPercent = $video->duration > 0
                        ? min(100, ($progress->watched_duration / $video->duration) * 100)
                        : 0;
                }
            }

            return [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'duration' => $video->duration > 0 ? gmdate('i:s', $video->duration) : '00:00',
                'progress' => $progressPercent,
                'status' => $status,
                'locked' => false, // For now, all unlocked
                'category' => $video->category ? $video->category->name : 'Uncategorized',
            ];
        });

        $selectedCategory = $categoryId ? VideoCategory::find($categoryId) : null;

        return view('intern.dashboard', compact('videos', 'categories', 'selectedCategory'));
    }
}
