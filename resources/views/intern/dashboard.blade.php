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
                    <div class="stat-number">{{ $inProgressVideos->count() + $recentlyViewedVideos->count() }}</div>
                    <div class="stat-label">My Videos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">{{ $recentlyViewedVideos->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number">{{ $inProgressVideos->count() }}</div>
                    <div class="stat-label">In Progress</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number">
                        @if($inProgressVideos->count() + $recentlyViewedVideos->count() > 0)
                            {{ round(($recentlyViewedVideos->count() / ($inProgressVideos->count() + $recentlyViewedVideos->count())) * 100) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="stat-label">Completion Rate</div>
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

            <!-- Continue Learning Section -->
            @if($inProgressVideos->count() > 0)
                <div class="section-header">
                    <h2 style="color: var(--text-primary); font-size: 1.5rem; font-weight: 600; margin: 0;">
                        <i class="fas fa-play-circle" style="margin-right: 0.5rem; color: var(--warning);"></i>
                        Continue Learning
                    </h2>
                    <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">
                        Pick up where you left off
                    </p>
                </div>

                <div class="video-grid">
                    @foreach($inProgressVideos as $video)
                        <div class="video-card-wrapper">
                            <a href="{{ route('video.watch', $video['id']) }}" class="video-card">
                                <div class="video-thumbnail">
                                    <div class="video-play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <div class="video-duration-badge">
                                        <i class="fas fa-clock"></i>
                                        {{ $video['duration'] }}
                                    </div>
                                </div>

                                <div class="video-content">
                                    <h3 class="video-title">{{ $video['title'] }}</h3>
                                    @if($video['description'])
                                        <p class="video-description">{{ Str::limit($video['description'], 80) }}</p>
                                    @endif

                                    <div class="video-meta">
                                        <div class="status-indicator">
                                            <span class="status-dot status-in-progress"></span>
                                            <span class="status-text">{{ $video['progress'] }}% Complete</span>
                                        </div>

                                        @if($video['category'] !== 'Uncategorized')
                                            <div class="category-badge">
                                                <i class="fas fa-tag"></i>
                                                {{ $video['category'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $video['progress'] }}%;"></div>
                                        </div>
                                    </div>

                                    <div class="video-actions">
                                        <span class="watch-btn">
                                            <i class="fas fa-play-circle"></i>
                                            Continue Watching
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Recently Viewed Section -->
            @if($recentlyViewedVideos->count() > 0)
                <div class="section-header" style="margin-top: 3rem;">
                    <h2 style="color: var(--text-primary); font-size: 1.5rem; font-weight: 600; margin: 0;">
                        <i class="fas fa-history" style="margin-right: 0.5rem; color: var(--success);"></i>
                        Recently Viewed
                    </h2>
                    <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">
                        Review your completed videos
                    </p>
                </div>

                <div class="video-grid">
                    @foreach($recentlyViewedVideos as $video)
                        <div class="video-card-wrapper">
                            <a href="{{ route('video.watch', $video['id']) }}" class="video-card">
                                <div class="video-thumbnail">
                                    <div class="video-play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <div class="video-duration-badge">
                                        <i class="fas fa-clock"></i>
                                        {{ $video['duration'] }}
                                    </div>
                                    <div class="completed-badge">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>

                                <div class="video-content">
                                    <h3 class="video-title">{{ $video['title'] }}</h3>
                                    @if($video['description'])
                                        <p class="video-description">{{ Str::limit($video['description'], 80) }}</p>
                                    @endif

                                    <div class="video-meta">
                                        <div class="status-indicator">
                                            <span class="status-dot status-completed"></span>
                                            <span class="status-text">Completed</span>
                                        </div>

                                        @if($video['category'] !== 'Uncategorized')
                                            <div class="category-badge">
                                                <i class="fas fa-tag"></i>
                                                {{ $video['category'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 100%; background-color: var(--success);"></div>
                                        </div>
                                    </div>

                                    <div class="video-actions">
                                        <span class="watch-btn">
                                            <i class="fas fa-redo"></i>
                                            Review Video
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Empty State -->
            @if($inProgressVideos->isEmpty() && $recentlyViewedVideos->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3 class="empty-state-title">Start Your Learning Journey</h3>
                    <p class="empty-state-description">
                        You haven't started watching any videos yet. Browse our video library to begin learning!
                    </p>
                    <a href="{{ route('intern.videos.all') }}" class="btn btn-primary">
                        <i class="fas fa-play"></i>
                        Browse Videos
                    </a>
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

        /* Completed Badge */
        .completed-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .empty-state-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state-description {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Section Spacing */
        .section-header {
            margin-bottom: 2rem;
        }

        .section-header+.video-grid {
            margin-bottom: 3rem;
        }
    </style>
@endsection