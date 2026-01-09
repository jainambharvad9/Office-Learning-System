@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card bg-light" style="background-color: var(--card-bg) !important; border-color: var(--border) !important;">
                <div class="card-header bg-primary text-white" style="background-color: var(--primary) !important;">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel"></i> Filters
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('intern.videos.all') }}" id="filterForm">
                        <!-- Search Input -->
                        <div class="mb-4">
                            <label class="form-label fw-500" style="color: var(--text-primary);">
                                <i class="bi bi-search"></i> Search Videos
                            </label>
                            <input type="text" name="search" class="form-control" placeholder="Video title..." 
                                   value="{{ request('search') }}" style="background-color: var(--input-bg); border-color: var(--border); color: var(--text-primary);">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-500" style="color: var(--text-primary);">
                                <i class="bi bi-folder"></i> Category
                            </label>
                            <select name="category" class="form-select" style="background-color: var(--input-bg); border-color: var(--border); color: var(--text-primary);">
                                <option value="">All Categories</option>
                                @forelse($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @empty
                                    <option disabled>No categories</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-500" style="color: var(--text-primary);">
                                <i class="bi bi-check-circle"></i> Status
                            </label>
                            <select name="status" class="form-select" style="background-color: var(--input-bg); border-color: var(--border); color: var(--text-primary);">
                                <option value="">All Videos</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                    <i class="bi bi-play-circle"></i> In Progress
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    <i class="bi bi-circle"></i> Not Started
                                </option>
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-4">
                            <label class="form-label fw-500" style="color: var(--text-primary);">
                                <i class="bi bi-sort-down"></i> Sort By
                            </label>
                            <select name="sort" class="form-select" style="background-color: var(--input-bg); border-color: var(--border); color: var(--text-primary);">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>
                                    Latest First
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                    Oldest First
                                </option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>
                                    Title (A-Z)
                                </option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" style="background-color: var(--primary) !important; border-color: var(--primary) !important;">
                                <i class="bi bi-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('intern.videos.all') }}" class="btn btn-secondary" style="background-color: var(--secondary) !important; border-color: var(--secondary) !important;">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Filters Summary -->
            @if(request('search') || request('category') || request('status') || request('sort') != 'latest')
                <div class="card mt-3" style="background-color: var(--card-bg) !important; border-color: var(--border) !important; border-left: 4px solid var(--primary);">
                    <div class="card-body">
                        <p class="text-muted mb-2" style="color: var(--text-secondary) !important;">Active Filters:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @if(request('search'))
                                <span class="badge bg-info" style="background-color: var(--info) !important;">
                                    Search: {{ request('search') }}
                                </span>
                            @endif
                            @if(request('category'))
                                <span class="badge bg-warning" style="background-color: var(--warning) !important;">
                                    Category: {{ $categories->find(request('category'))?->name ?? 'Unknown' }}
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="badge bg-success" style="background-color: var(--success) !important;">
                                    Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            @php
                // Group videos by category and sort by part_number within each category
                $videosByCategory = collect($videos)->groupBy(function($video) {
                    return $video['category'] ? $video['category']->name : 'Uncategorized';
                })->map(function($categoryVideos) {
                    return $categoryVideos->sortBy('part_number');
                })->sortKeys();
            @endphp

            <!-- Header -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1" style="color: var(--text-primary);">
                            <i class="bi bi-film"></i> All Videos
                        </h2>
                        <p class="text-muted" style="color: var(--text-secondary) !important;">
                            {{ $videosByCategory->count() }} categor{{ $videosByCategory->count() > 1 ? 'ies' : 'y' }} •
                            Total: <strong>{{ $videos instanceof \Illuminate\Pagination\Paginator ? $videos->total() : collect($videos)->count() }}</strong> videos
                        </p>
                    </div>
                </div>
            </div>

            <!-- Videos Grid -->

            @forelse($videosByCategory as $categoryName => $categoryVideos)
                <!-- Category Section -->
                <div class="category-section mb-5">
                    <div class="category-header mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="category-icon">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                            <div>
                                <h3 class="category-title mb-1" style="color: var(--text-primary);">
                                    {{ $categoryName }}
                                </h3>
                                <p class="category-subtitle mb-0" style="color: var(--text-secondary); font-size: 0.9rem;">
                                    {{ $categoryVideos->count() }} video{{ $categoryVideos->count() > 1 ? 's' : '' }} •
                                    Total duration: {{ $categoryVideos->sum(function($video) { return $video['duration'] ? strtotime($video['duration']) - strtotime('00:00') : 0; }) ? gmdate('H:i:s', $categoryVideos->sum(function($video) { return $video['duration'] ? strtotime($video['duration']) - strtotime('00:00') : 0; })) : 'Unknown' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Videos in this category -->
                    <div class="videos-grid">
                        @foreach($categoryVideos as $video)
                            <div class="video-card-wrapper">
                                <div class="card h-100 video-card" style="background-color: var(--card-bg) !important; border-color: var(--border) !important; transition: transform 0.2s, box-shadow 0.2s;">
                                    <div class="card-body p-0">
                                        <!-- Video Thumbnail -->
                                        <div class="video-thumbnail position-relative overflow-hidden" style="height: 180px; background: linear-gradient(135deg, var(--primary), var(--secondary));">
                                            @if(isset($video['thumbnail_url']) && $video['thumbnail_url'])
                                                <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}"
                                                     class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <i class="bi bi-film" style="font-size: 2.5rem; color: rgba(255,255,255,0.5);"></i>
                                                </div>
                                            @endif

                                            <!-- Part Number Badge -->
                                            <div class="part-badge position-absolute top-0 start-0 m-2">
                                                <span class="badge bg-primary" style="background-color: var(--primary) !important; font-size: 0.8rem;">
                                                    Part {{ $video['part_number'] ?? 1 }}
                                                </span>
                                            </div>

                                            <!-- Progress Badge -->
                                            {{-- @if($video['progress_percentage'] > 0)
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge" style="background-color: {{ $video['status'] === 'completed' ? 'var(--success)' : ($video['status'] === 'in_progress' ? 'var(--warning)' : 'var(--secondary)') }};">
                                                        {{ $video['progress_percentage'] }}%
                                                    </span>
                                                </div>
                                            @endif --}}

                                            <!-- Play Button Overlay -->
                                            <a href="{{ route('video.watch', $video['id']) }}"
                                               class="play-overlay position-absolute top-50 start-50 translate-middle btn btn-light btn-lg rounded-circle"
                                               style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                                                <i class="bi bi-play-fill" style="font-size: 1.2rem;"></i>
                                            </a>
                                        </div>

                                        <!-- Video Details -->
                                        <div class="p-3">
                                            <!-- Title -->
                                            <h6 class="card-title mb-2" style="color: var(--text-primary); font-weight: 600; line-height: 1.3;">
                                                {{ $video['title'] }}
                                            </h6>

                                            <!-- Description -->
                                            <p class="card-text mb-3" style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4;">
                                                {{ Str::limit($video['description'], 100) }}
                                            </p>

                                            <!-- Meta Info -->
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex gap-3">
                                                    @if($video['duration'])
                                                        <small style="color: var(--text-secondary); font-size: 0.8rem;">
                                                            <i class="bi bi-clock"></i> {{ $video['duration'] }}
                                                        </small>
                                                    @endif
                                                    <small style="color: var(--text-secondary); font-size: 0.8rem;">
                                                        <i class="bi bi-calendar"></i> {{ $video['created_at']->format('M d') }}
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small style="color: var(--text-secondary); font-size: 0.8rem;">Progress</small>
                                                    <small style="color: var(--text-secondary); font-size: 0.8rem;">{{ $video['progress_percentage'] }}%</small>
                                                </div>
                                                <div class="progress" style="height: 4px; background-color: var(--border);">
                                                    <div class="progress-bar"
                                                         style="width: {{ $video['progress_percentage'] }}%; background-color: var(--primary);"
                                                         role="progressbar">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Status and Action -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @switch($video['status'])
                                                        @case('completed')
                                                            <span class="badge bg-success" style="font-size: 0.75rem;">
                                                                <i class="bi bi-check-circle"></i> Completed
                                                            </span>
                                                            @break
                                                        @case('in_progress')
                                                            <span class="badge bg-warning" style="font-size: 0.75rem;">
                                                                <i class="bi bi-play-circle"></i> In Progress
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary" style="font-size: 0.75rem;">
                                                                <i class="bi bi-circle"></i> Not Started
                                                            </span>
                                                    @endswitch
                                                </div>
                                                <a href="{{ route('video.watch', $video['id']) }}"
                                                   class="btn btn-sm btn-primary" style="background-color: var(--primary) !important; border-color: var(--primary) !important; font-size: 0.8rem; padding: 0.375rem 0.75rem;">
                                                    <i class="bi bi-play"></i> {{ $video['status'] === 'completed' ? 'Review' : 'Watch' }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center py-5" style="background-color: var(--info) !important; border-color: var(--primary) !important; color: var(--text-primary) !important;">
                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">No Videos Found</h5>
                    <p class="mb-0">Try adjusting your filters or search terms</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($videos instanceof \Illuminate\Pagination\Paginator && !$videosByCategory->isEmpty())
                <div class="d-flex justify-content-center mt-5">
                    {{ $videos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .video-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    /* Category Section Styles */
    .category-section {
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        background-color: var(--card-bg);
    }

    .category-header {
        border-bottom: 2px solid var(--primary);
        padding-bottom: 1rem;
    }

    .category-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .category-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .category-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }

    /* Videos Grid */
    .videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .video-card-wrapper {
        width: 100%;
    }

    .video-thumbnail {
        border-radius: var(--radius) var(--radius) 0 0;
    }

    .part-badge {
        z-index: 2;
    }

    .play-overlay {
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 3;
    }

    .video-thumbnail:hover .play-overlay {
        opacity: 1;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .videos-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .category-section {
            padding: 1.5rem;
        }

        .category-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .category-title {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 576px) {
        .videos-grid {
            grid-template-columns: 1fr;
        }

        .category-section {
            padding: 1rem;
        }
    }
</style>
@endsection
