@extends('layouts.app')

@section('title', 'Admin Dashboard - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Admin Dashboard</h1>
                <p class="dashboard-subtitle">Manage your learning platform and monitor intern progress.</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">{{ $totalInterns }}</div>
                    <div class="stat-label">Total Interns</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-number">{{ $totalVideos }}</div>
                    <div class="stat-label">Total Videos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">{{ $completedVideos }}</div>
                    <div class="stat-label">Completed Videos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">{{ round($internProgress->avg('progress'), 1) }}%</div>
                    <div class="stat-label">Avg Progress</div>
                </div>
            </div>

            <!-- Recent Activity / Intern Progress -->
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary);">Intern Progress Overview</h3>
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
                                        Watch Count</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Progress</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($internProgress as $intern)
                                    <tr style="border-bottom: 1px solid var(--border-light);">
                                        <td style="padding: 0.75rem; color: var(--text-primary);">{{ $intern['name'] }}</td>
                                        <td style="padding: 0.75rem; color: var(--text-primary);">
                                            {{ $intern['watch_count'] }} / {{ $intern['total_videos'] }}
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div class="progress-bar" style="flex: 1; height: 8px;">
                                                    <div class="progress-fill" style="width: {{ $intern['progress'] }}%;"></div>
                                                </div>
                                                <span
                                                    style="font-size: 0.9rem; color: var(--text-secondary);">{{ $intern['progress'] }}%</span>
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <span
                                                class="status-badge status-{{ strtolower(str_replace(' ', '-', $intern['status'])) }}">
                                                {{ $intern['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection