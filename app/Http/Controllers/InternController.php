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

        // Get all active categories
        $categories = VideoCategory::active()->get();

        // Get videos with progress (recently viewed or in progress)
        $videosWithProgress = VideoProgress::where('user_id', $user->id)
            ->with(['video.category'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($progress) {
                $video = $progress->video;

                $status = 'Pending';
                $progressPercent = 0;

                if ($progress->is_completed) {
                    $status = 'Completed';
                    $progressPercent = 100;
                } else {
                    $status = 'In Progress';
                    $progressPercent = $video->duration > 0
                        ? min(100, round(($progress->watched_duration / $video->duration) * 100))
                        : 0;
                }

                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'duration' => $video->duration > 0 ? gmdate('i:s', $video->duration) : '00:00',
                    'progress' => $progressPercent,
                    'status' => $status,
                    'locked' => false,
                    'category' => $video->category ? $video->category->name : 'Uncategorized',
                    'last_viewed' => $progress->updated_at,
                ];
            });

        // Separate into "In Progress" and "Recently Viewed" (completed)
        $inProgressVideos = $videosWithProgress->where('status', 'In Progress')->take(6);
        $recentlyViewedVideos = $videosWithProgress->where('status', 'Completed')->take(6);

        return view('intern.dashboard', compact('inProgressVideos', 'recentlyViewedVideos', 'categories'));
    }

    public function allVideos(Request $request)
    {
        $user = Auth::user();
        $categoryId = $request->get('category');
        $status = $request->get('status'); // 'completed', 'in_progress', 'pending'
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'latest'); // 'latest', 'oldest', 'title'

        // Get all active categories
        $categories = VideoCategory::active()->get();

        // Filter videos
        $videosQuery = Video::query();

        if ($categoryId) {
            $videosQuery->where('category_id', $categoryId);
        }

        if ($search) {
            $videosQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter before pagination
        if ($status) {
            // We need to fetch all videos first to apply status filter, then paginate
            $allVideos = $videosQuery->get()->map(function ($video) use ($user, $status) {
                $progress = VideoProgress::where('user_id', $user->id)
                    ->where('video_id', $video->id)
                    ->first();

                $videoStatus = 'Pending';
                $progressPercent = 0;

                if ($progress) {
                    if ($progress->is_completed) {
                        $videoStatus = 'Completed';
                        $progressPercent = 100;
                    } else {
                        $videoStatus = 'In Progress';
                        $progressPercent = $video->duration > 0
                            ? min(100, round(($progress->watched_duration / $video->duration) * 100))
                            : 0;
                    }

                    // Debug logging
                    Log::info("Video {$video->id}: status={$videoStatus}, progressPercent={$progressPercent}, watched={$progress->watched_duration}, duration={$video->duration}, completed={$progress->is_completed}");
                }

                // Filter by status if specified
                if ($this->getStatusKey($videoStatus) !== $status) {
                    return null;
                }

                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'thumbnail_url' => $video->thumbnail_url ?? null,
                    'duration' => $video->duration > 0 ? gmdate('i:s', $video->duration) : '00:00',
                    'progress_percentage' => $progressPercent,
                    'status' => strtolower(str_replace(' ', '_', $videoStatus)),
                    'category' => $video->category,
                    'part_number' => $video->part_number ?? 1,
                    'created_at' => $video->created_at,
                ];
            })->filter()->values();

            $total = $allVideos->count();
            $page = $request->get('page', 1);
            $perPage = 12;
            $items = $allVideos->slice(($page - 1) * $perPage, $perPage);

            $videos = new \Illuminate\Pagination\Paginator(
                $items,
                $perPage,
                $page,
                [
                    'path' => route('intern.videos.all'),
                    'query' => $request->query(),
                ]
            );
        } else {
            // Apply sorting
            switch ($sortBy) {
                case 'oldest':
                    $videosQuery->oldest();
                    break;
                case 'title':
                    $videosQuery->orderBy('title', 'asc');
                    break;
                default: // latest
                    $videosQuery->latest();
            }

            $paginatedVideos = $videosQuery->paginate(12);

            $videos = $paginatedVideos->map(function ($video) use ($user) {
                $progress = VideoProgress::where('user_id', $user->id)
                    ->where('video_id', $video->id)
                    ->first();

                $videoStatus = 'Pending';
                $progressPercent = 0;

                if ($progress) {
                    if ($progress->is_completed) {
                        $videoStatus = 'Completed';
                        $progressPercent = 100;
                    } else {
                        $videoStatus = 'In Progress';
                        $progressPercent = $video->duration > 0
                            ? min(100, round(($progress->watched_duration / $video->duration) * 100))
                            : 0;
                    }
                }

                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'thumbnail_url' => $video->thumbnail_url ?? null,
                    'duration' => $video->duration > 0 ? gmdate('i:s', $video->duration) : '00:00',
                    'progress_percentage' => $progressPercent,
                    'status' => strtolower(str_replace(' ', '_', $videoStatus)),
                    'category' => $video->category,
                    'part_number' => $video->part_number ?? 1,
                    'created_at' => $video->created_at,
                ];
            });
        }

        $selectedCategory = $categoryId ? VideoCategory::find($categoryId) : null;

        return view('intern.videos.index', compact(
            'videos',
            'categories',
            'selectedCategory',
            'search',
            'status',
            'sortBy'
        ));
    }

    private function getStatusKey($status)
    {
        return strtolower(str_replace(' ', '_', $status));
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
