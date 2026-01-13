@extends('layouts.app')

@section('title', 'Reports - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Training Reports</h1>
                <p class="dashboard-subtitle">Monitor intern progress and completion statistics across all training videos.
                </p>
            </div>

            <!-- Summary Statistics -->
            {{-- <div class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">{{ $reports->unique('intern_name')->count() }}</div>
                    <div class="stat-label">Active Interns</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-number">{{ $reports->unique('video_name')->count() }}</div>
                    <div class="stat-label">Training Videos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">{{ $reports->where('completion_status', 'Completed')->count() }}</div>
                    <div class="stat-label">Completions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number">{{ round($reports->avg('watch_percentage'), 1) }}%</div>
                    <div class="stat-label">Avg Completion</div>
                </div>
            </div> --}}

            <!-- Filters and Search -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-filter"></i>
                        Filters & Search
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports') }}" id="filterForm">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            <!-- Search -->
                            <div>
                                <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    <i class="fas fa-search"></i> Search
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                       placeholder="Search by intern or video..."
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
                            <div>
                                <label for="intern" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    <i class="fas fa-user"></i> Intern
                                </label>
                                <select id="intern" name="intern" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                                    <option value="">All Interns</option>
                                    @foreach($interns as $internUser)
                                    <option value="{{ $internUser->id }}" {{ request('intern') == $internUser->id ? 'selected' : '' }}>
                                        {{ $internUser->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                            @if(request()->hasAny(['search', 'category', 'intern']))
                                <span style="color: var(--text-secondary); font-size: 0.9rem;">
                                    Showing {{ $reports->count() }} results
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Detailed Reports Table -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-chart-bar"></i>
                        Detailed Progress Reports
                    </h3>
                </div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ route('admin.reports') . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'intern_name', 'direction' => (request('order_by') === 'intern_name' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Intern Name
                                            @if(request('order_by') === 'intern_name')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ route('admin.reports') . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'video_name', 'direction' => (request('order_by') === 'video_name' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Video Title
                                            @if(request('order_by') === 'video_name')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Watch Time
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ route('admin.reports') . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'watch_count', 'direction' => (request('order_by') === 'watch_count' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Watch Count
                                            @if(request('order_by') === 'watch_count')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        <a href="{{ route('admin.reports') . '?' . http_build_query(array_merge(request()->query(), ['order_by' => 'completion_status', 'direction' => (request('order_by') === 'completion_status' && request('direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                           style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                            Status
                                            @if(request('order_by') === 'completion_status')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort" style="opacity: 0.5;"></i>
                                            @endif
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr style="border-bottom: 1px solid var(--border-light);">
                                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                            {{ $report['intern_name'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">{{ $report['video_name'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary); font-size: 0.9rem;">
                                            {{ $report['watch_time'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                            {{ $report['watch_count'] }}
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report['completion_status'])) }}">
                                                {{ $report['completion_status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($reports->hasPages())
                        <div style="margin-top: 3rem; text-align: center;">
                            <div style="display: inline-block; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    {{-- Previous Button --}}
                                    @if($reports->currentPage() > 1)
                                        <a href="{{ $reports->appends(request()->query())->url(1) }}" style="padding: 0.75rem 1rem; border-right: 1px solid #ddd; color: #0066cc; text-decoration: none; font-size: 14px; display: inline-block;">« Previous</a>
                                    @else
                                        <span style="padding: 0.75rem 1rem; border-right: 1px solid #ddd; color: #ccc; font-size: 14px; display: inline-block;">« Previous</span>
                                    @endif

                                    {{-- Page Numbers --}}
                                    <div style="display: flex; align-items: center;">
                                        @php
                                            $totalPages = $reports->lastPage();
                                            $currentPage = $reports->currentPage();
                                            $start = max(1, $currentPage - 1);
                                            $end = min($totalPages, $currentPage + 1);
                                            if ($end - $start < 2) {
                                                $start = max(1, $end - 2);
                                                $end = min($totalPages, $start + 2);
                                            }
                                        @endphp

                                        @if($start > 1)
                                            <a href="{{ $reports->appends(request()->query())->url(1) }}" style="padding: 0.75rem 1rem; border-right: 1px solid #ddd; color: #0066cc; text-decoration: none; font-size: 14px; display: inline-block;">1</a>
                                            @if($start > 2)
                                                <span style="padding: 0.75rem 0.5rem; border-right: 1px solid #ddd; color: #999; font-size: 14px; display: inline-block;">...</span>
                                            @endif
                                        @endif

                                        @for($page = $start; $page <= $end; $page++)
                                            @if($page == $currentPage)
                                                <span style="padding: 0.75rem 1rem; border-right: 1px solid #ddd; background: #0066cc; color: white; font-weight: bold; font-size: 14px; display: inline-block;">{{ $page }}</span>
                                            @else
                                                <a href="{{ $reports->appends(request()->query())->url($page) }}" style="padding: 0.75rem 1rem; border-right: 1px solid #ddd; color: #0066cc; text-decoration: none; font-size: 14px; display: inline-block;">{{ $page }}</a>
                                            @endif
                                        @endfor

                                        @if($end < $totalPages)
                                            @if($end < $totalPages - 1)
                                                <span style="padding: 0.75rem 0.5rem; border-right: 1px solid #ddd; color: #999; font-size: 14px; display: inline-block;">...</span>
                                            @endif
                                            <a href="{{ $reports->appends(request()->query())->url($totalPages) }}" style="padding: 0.75rem 1rem; border-right: 1px solid #ddd; color: #0066cc; text-decoration: none; font-size: 14px; display: inline-block;">{{ $totalPages }}</a>
                                        @endif
                                    </div>

                                    {{-- Next Button --}}
                                    @if($reports->hasMorePages())
                                        <a href="{{ $reports->appends(request()->query())->url($reports->currentPage() + 1) }}" style="padding: 0.75rem 1rem; color: #0066cc; text-decoration: none; font-size: 14px; display: inline-block;">Next »</a>
                                    @else
                                        <span style="padding: 0.75rem 1rem; color: #ccc; font-size: 14px; display: inline-block;">Next »</span>
                                    @endif
                                </div>
                            </div>
                            <div style="margin-top: 1rem; color: #666; font-size: 14px;">
                                Showing {{ ($reports->currentPage() - 1) * $reports->perPage() + 1 }} to {{ min($reports->currentPage() * $reports->perPage(), $reports->total()) }} of {{ $reports->total() }} results
                            </div>
                        </div>
                    @endif

                    @if($reports->isEmpty())
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No reports available yet.</p>
                            <small>Reports will appear as interns start watching videos.</small>
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
    const filterInputs = filterForm.querySelectorAll('select');

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Small delay to prevent too many requests
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(() => {
                filterForm.submit();
            }, 300);
        });
    });

    // Search input with debounce
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 800);
        });
    }
});
</script>
@endpush