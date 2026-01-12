@extends('layouts.app')

@section('title', 'Video Management - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Video Management</h1>
                <p class="dashboard-subtitle">Manage uploaded videos, view statistics, and delete content.</p>
            </div>

            <!-- Action Buttons -->
            <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                <a href="{{ route('admin.upload.form') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Upload New Video
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Videos Table -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-video"></i>
                        All Videos ({{ $videos->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Title</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Duration</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        File Size</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Views</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Completed</th>
                                    <th
                                        style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                        Upload Date</th>
                                    <th
                                        style="padding: 0.75rem; text-align: center; font-weight: 600; color: var(--text-primary);">
                                        Actions</th>
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
                                            {{ $video['duration'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">
                                            {{ $video['file_size'] }}
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                            {{ $video['total_views'] }}
                                        </td>
                                        <td style="padding: 0.75rem;">
                                            <span
                                                style="background: var(--success); color: white; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
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

                    @if($videos->isEmpty())
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-video-slash" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No videos uploaded yet.</p>
                            <a href="{{ route('admin.upload.form') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-plus"></i> Upload Your First Video
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection