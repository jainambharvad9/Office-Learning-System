<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoProgress;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function searchVideos(Request $request)
    {
        try {
            $query = $request->get('q');

            if (!$query || strlen($query) < 2) {
                return response()->json([]);
            }

            $videos = Video::where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%');
            })
                ->with('category')
                ->limit(10)
                ->get()
                ->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'description' => Str::limit($video->description, 100),
                        'category' => $video->category?->name ?? 'No Category',
                        'url' => route('video.watch', $video->id),
                        'thumbnail' => $video->thumbnail_url ?? null
                    ];
                });

            return response()->json($videos);
        } catch (\Exception $e) {
            Log::error('Video search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed', 'message' => $e->getMessage()], 500);
        }
    }
}