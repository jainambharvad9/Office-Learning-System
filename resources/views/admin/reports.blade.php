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
        <div class="stats-grid" style="margin-bottom: 2rem;">
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
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Intern Name</th>
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Video Title</th>
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Watch Time</th>
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Progress</th>
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Status</th>
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
                                        {{ $report['watched_duration'] }} / {{ $report['total_duration'] }}
                                    </td>
                                    <td style="padding: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div class="progress-bar" style="flex: 1; height: 8px;">
                                                <div class="progress-fill"
                                                    style="width: {{ $report['watch_percentage'] }}%;"></div>
                                            </div>
                                            <span
                                                style="font-size: 0.9rem; color: var(--text-secondary); font-weight: 500;">{{ $report['watch_percentage'] }}%</span>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem;">
                                        <span
                                            class="status-badge status-{{ strtolower(str_replace(' ', '-', $report['completion_status'])) }}">
                                            {{ $report['completion_status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reports->isEmpty())
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>No reports available yet.</p>
                        <small>Reports will appear as interns start watching videos.</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- User Watch Counts -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-eye"></i>
                    User Watch Counts
                </h3>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Intern Name</th>
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Videos Watched</th>
                                <th
                                    style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                    Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userWatchCounts as $user)
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                        {{ $user['name'] }}
                                    </td>
                                    <td style="padding: 0.75rem; color: var(--text-primary);">
                                        {{ $user['watch_count'] }} / {{ $user['total_videos'] }}
                                    </td>
                                    <td style="padding: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div class="progress-bar" style="flex: 1; height: 8px;">
                                                <div class="progress-fill"
                                                    style="width: {{ $user['total_videos'] > 0 ? ($user['watch_count'] / $user['total_videos']) * 100 : 0 }}%;">
                                                </div>
                                            </div>
                                            <span
                                                style="font-size: 0.9rem; color: var(--text-secondary); font-weight: 500;">
                                                {{ $user['total_videos'] > 0 ? round(($user['watch_count'] / $user['total_videos']) * 100, 1) : 0 }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>