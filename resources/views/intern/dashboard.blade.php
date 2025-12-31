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