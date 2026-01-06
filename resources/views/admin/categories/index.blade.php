@extends('layouts.app')

@section('title', 'Video Categories - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Video Categories</h1>
                <p class="dashboard-subtitle">Manage video categories for better organization.</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 2rem;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom: 2rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Add Category Button -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div></div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Category
                </a>
            </div>

            <!-- Categories Table -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-tags"></i>
                        Categories ({{ $categories->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--border);">
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            ID</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Name</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Description</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Videos Count</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Status</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Created</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr style="border-bottom: 1px solid var(--border-light);">
                                            <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                                {{ $category->id }}</td>
                                            <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                                <strong>{{ $category->name }}</strong>
                                            </td>
                                            <td style="padding: 0.75rem; color: var(--text-secondary);">
                                                {{ $category->description ? Str::limit($category->description, 50) : '-' }}
                                            </td>
                                            <td style="padding: 0.75rem;">
                                                <span class="status-badge status-inprogress">{{ $category->videos_count }}</span>
                                            </td>
                                            <td style="padding: 0.75rem;">
                                                @if($category->is_active)
                                                    <span class="status-badge status-completed">Active</span>
                                                @else
                                                    <span class="status-badge status-pending">Inactive</span>
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem; color: var(--text-secondary);">
                                                {{ $category->created_at->format('M d, Y') }}</td>
                                            <td style="padding: 0.75rem;">
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    @if($category->videos_count == 0)
                                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                                            style="display: inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                            title="Cannot delete category with videos">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Categories Found</h4>
                            <p class="text-muted">Start by creating your first video category.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection