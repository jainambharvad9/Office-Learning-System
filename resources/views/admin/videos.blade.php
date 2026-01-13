@extends('layouts.app')

@section('title', 'Video Management - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Video Management</h1>
                <p class="dashboard-subtitle">Manage uploaded videos, view statistics, and delete content.</p>
            </div>

            <!-- Filters and Search -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-filter"></i>
                        Filters & Search
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.videos') }}" id="filterForm">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            <!-- Search -->
                            <div>
                                <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    <i class="fas fa-search"></i> Search Videos
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                       placeholder="Search by title or description..."
                                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    <i class="fas fa-tag"></i> Category
                                </label>
                                <select id="category" name="category" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Intern Filter -->
                            {{-- <div>
                                <label for="intern" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    <i class="fas fa-user"></i> Accessed by Intern
                                </label>
                                <select id="intern" name="intern" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                                    <option value="">All Videos</option>
                                    @foreach($interns as $intern)
                                        <option value="{{ $intern->id }}" {{ request('intern') == $intern->id ? 'selected' : '' }}>
                                            {{ $intern->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <!-- Per Page -->
                            <div>
                                <label for="per_page" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    <i class="fas fa-list"></i> Items per page
                                </label>
                                <select id="per_page" name="per_page" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                                    <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.videos') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                            @if(request()->hasAny(['search', 'category', 'intern']))
                                <span style="color: var(--text-secondary); font-size: 0.9rem;">
                                    Showing {{ $videos->count() }} of {{ $videos->total() }} videos
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Videos Table -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-video"></i>
                        Videos ({{ $videos->total() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ $videos->url($videos->currentPage()) . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'title', 'direction' => (request('order_by') === 'title' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Title
                                            @if(request('order_by') === 'title')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Category
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Duration
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        File Size
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ $videos->url($videos->currentPage()) . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'total_views', 'direction' => (request('order_by') === 'total_views' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Views
                                            @if(request('order_by') === 'total_views')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ $videos->url($videos->currentPage()) . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'completed_count', 'direction' => (request('order_by') === 'completed_count' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Completed
                                            @if(request('order_by') === 'completed_count')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ $videos->url($videos->currentPage()) . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'created_at', 'direction' => (request('order_by') === 'created_at' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Upload Date
                                            @if(request('order_by') === 'created_at' || !request('order_by'))
                                                <i class="fas fa-sort-{{ (request('order_by') === 'created_at' && request('direction') === 'asc') || !request('order_by') ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: var(--text-primary);">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($videos as $video)
                                    <tr style="border-bottom: 1px solid var(--border-light);">
                                        <td style="padding: 0.75rem; color: var(--text-primary);">
                                            <div style="font-weight: 500;">{{ $video['title'] }}</div>
                                            @if($video['description'])
                                                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                                    {{ Str::limit($video['description'], 60) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">
                                            <span style="background: var(--primary-light); color: var(--primary); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
                                                {{ $video['category'] }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">
                                            {{ $video['duration'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">
                                            {{ $video['file_size'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                            {{ $video['total_views'] }}
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <span style="background: var(--success); color: white; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
                                                {{ $video['completed_count'] }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">
                                            {{ $video['upload_date'] }}
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center;">
                                            <a href="{{ route('admin.videos.edit', $video['id']) }}" class="btn btn-sm"
                                                style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: var(--radius-sm); text-decoration: none; font-size: 0.85rem; margin-right: 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.videos.delete', $video['id']) }}"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this video? This action cannot be undone and will remove all progress data for this video.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    style="background: var(--error); border: none; color: white; padding: 0.5rem 1rem; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.85rem;">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($videos->hasPages())
                        <div style="margin-top: 2rem; display: flex; justify-content: center;">
                            @if($videos->currentPage() > 1)
                                <a href="{{ $videos->url($videos->currentPage() - 1) . '?' . http_build_query(request()->query()) }}" style="display: inline-block; padding: 0.5rem 1rem; margin-right: 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; border: none; cursor: pointer;">Previous</a>
                            @else
                                <button disabled style="display: inline-block; padding: 0.5rem 1rem; margin-right: 1rem; background: #ddd; color: #999; border-radius: 4px; font-size: 14px; border: none; cursor: not-allowed;">Previous</button>
                            @endif

                            @if($videos->hasMorePages())
                                <a href="{{ $videos->url($videos->currentPage() + 1) . '?' . http_build_query(request()->query()) }}" style="display: inline-block; padding: 0.5rem 1rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; border: none; cursor: pointer;">Next</a>
                            @else
                                <button disabled style="display: inline-block; padding: 0.5rem 1rem; background: #ddd; color: #999; border-radius: 4px; font-size: 14px; border: none; cursor: not-allowed;">Next</button>
                            @endif
                        </div>
                    @endif

                    @if($videos->isEmpty())
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-video-slash" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No videos found matching your criteria.</p>
                            <a href="{{ route('admin.videos') }}" class="btn btn-secondary" style="margin-top: 1rem;">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('input, select');

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Small delay to prevent too many requests
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    });

    // Search input with debounce
    const searchInput = document.getElementById('search');
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 800);
    });
});
</script>
@endpush