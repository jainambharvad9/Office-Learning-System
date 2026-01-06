@extends('layouts.app')

@section('title', 'Intern Dashboard - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Welcome Back, {{ auth()->user()->name }}! 👋</h1>
                <p class="dashboard-subtitle">Continue your learning journey and unlock new opportunities in your career
                    development.</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-number">{{ $videos->count() }}</div>
                    <div class="stat-label">Total Videos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">{{ $videos->where('status', 'Completed')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number">{{ $videos->where('status', 'In Progress')->count() }}</div>
                    <div class="stat-label">In Progress</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number">{{ round($videos->avg('progress'), 1) }}%</div>
                    <div class="stat-label">Average Progress</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="{{ route('intern.dashboard') }}" class="action-card active">
                    <div class="action-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="action-content">
                        <h3>Videos</h3>
                        <p>Watch training videos</p>
                    </div>
                </a>

                <a href="{{ route('intern.quizzes.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="action-content">
                        <h3>Quizzes</h3>
                        <p>Test your knowledge</p>
                    </div>
                </a>
            </div>

            <!-- Videos Grid -->
            <div class="section-header">
                <h2 style="color: var(--text-primary); font-size: 1.5rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-video" style="margin-right: 0.5rem;"></i>
                    Available Videos
                </h2>
                <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">
                    Click on any video to start learning
                </p>
            </div>

            <!-- Category Filter -->
            @if($categories->count() > 0)
                <div style="margin-bottom: 2rem;">
                    <form method="GET" style="display: inline;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <label for="category" style="font-weight: 500; color: var(--text-primary); margin: 0;">
                                <i class="fas fa-filter"></i> Filter by Category:
                            </label>
                            <select name="category" id="category" class="form-input" style="width: auto; min-width: 200px;"
                                onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $selectedCategory && $selectedCategory->id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->videos_count }} videos)
                                    </option>
                                @endforeach
                            </select>
                            @if($selectedCategory)
                                <a href="{{ route('intern.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            @endif

            <div class="video-grid">
                @foreach($videos as $video)
                    <div class="video-card-wrapper">
                        <a href="{{ route('video.watch', $video['id']) }}"
                            class="video-card {{ $video['locked'] ? 'locked' : '' }}">
                            <div class="video-thumbnail">
                                @if($video['locked'])
                                    <div class="locked-overlay">
                                        <i class="fas fa-lock"></i>
                                        <span>Locked</span>
                                    </div>
                                @else
                                    <div class="video-play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <div class="video-duration-badge">
                                        <i class="fas fa-clock"></i>
                                        {{ $video['duration'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="video-content">
                                <h3 class="video-title">{{ $video['title'] }}</h3>
                                @if($video['description'])
                                    <p class="video-description">{{ Str::limit($video['description'], 80) }}</p>
                                @endif

                                <div class="video-meta">
                                    <div class="status-indicator">
                                        <span
                                            class="status-dot status-{{ strtolower(str_replace(' ', '-', $video['status'])) }}"></span>
                                        <span class="status-text">{{ $video['status'] }}</span>
                                    </div>

                                    @if($video['category'] !== 'Uncategorized')
                                        <div class="category-badge">
                                            <i class="fas fa-tag"></i>
                                            {{ $video['category'] }}
                                        </div>
                                    @endif

                                    @if(!$video['locked'] && $video['progress'] > 0)
                                        <div class="progress-indicator">
                                            {{ round($video['progress']) }}%
                                        </div>
                                    @endif
                                </div>

                                @if(!$video['locked'] && $video['progress'] > 0)
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $video['progress'] }}%;"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="video-actions">
                                    <span class="watch-btn">
                                        <i class="fas fa-play-circle"></i>
                                        {{ $video['status'] === 'Completed' ? 'Review' : 'Watch Now' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            @if($videos->isEmpty())
                <div class="card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;">
                        <i class="fas fa-video-slash"></i>
                    </div>
                    <h3 style="color: var(--text-secondary); margin-bottom: 0.5rem;">No Videos Available</h3>
                    <p style="color: var(--text-muted);">Check back later for new learning content!</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: var(--primary);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .category-badge i {
            font-size: 0.7rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--bg-primary);
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .action-card:hover {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
            text-decoration: none;
            color: var(--text-primary);
        }

        .action-card.active {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }

        .action-icon {
            width: 3rem;
            height: 3rem;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .action-content h3 {
            margin: 0 0 0.25rem 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .action-content p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Custom select styling to match form-input */
        select.form-input {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            cursor: pointer;
        }

        select.form-input:focus {
            outline: 0;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        /* Ensure options are visible in dropdown */
        select.form-input option {
            background: var(--bg-primary);
            color: var(--text-primary);
            padding: 0.5rem;
            margin: 0.25rem 0;
        }

        /* Force option text visibility in dark mode */
        select.form-input option:checked {
            background: var(--primary);
            color: white;
        }

        /* Light mode fallback */
        @media (prefers-color-scheme: light) {
            select.form-input {
                background-color: white;
                color: #333;
            }

            select.form-input option {
                background-color: white;
                color: #333;
            }

            select.form-input option:checked {
                background: var(--primary);
                color: white;
            }
        }

        /* Dark mode specific */
        @media (prefers-color-scheme: dark) {
            select.form-input {
                background-color: #1e293b;
                color: #f1f5f9;
            }

            select.form-input option {
                background-color: #1e293b;
                color: #f1f5f9;
            }

            select.form-input option:checked {
                background: #3b82f6;
                color: white;
            }
        }

        /* Override for data-theme attribute (manual theme toggle) */
        html[data-theme="dark"] select.form-input {
            background-color: #1e293b;
            color: #f1f5f9;
        }

        html[data-theme="dark"] select.form-input option {
            background-color: #1e293b;
            color: #f1f5f9;
        }

        html[data-theme="dark"] select.form-input option:checked {
            background: #3b82f6;
            color: white;
        }

        html[data-theme="light"] select.form-input {
            background-color: white;
            color: #333;
        }

        html[data-theme="light"] select.form-input option {
            background-color: white;
            color: #333;
        }

        html[data-theme="light"] select.form-input option:checked {
            background: var(--primary);
            color: white;
        }
    </style>
@endsection