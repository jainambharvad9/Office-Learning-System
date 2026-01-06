@extends('layouts.app')

@section('title', 'Edit Category - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Edit Category</h1>
                <p class="dashboard-subtitle">Update category information.</p>
            </div>

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('admin.categories') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>

            <!-- Edit Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3
                                style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-edit"></i>
                                Edit Category Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag"></i> Category Name <span style="color: var(--error);">*</span>
                                    </label>
                                    <input type="text" class="form-input @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $category->name) }}" required
                                        placeholder="e.g., GSTR, ITR, Income Tax, etc.">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                        <i class="fas fa-info-circle"></i> Enter a descriptive name for the category.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left"></i> Description
                                    </label>
                                    <textarea class="form-input form-textarea @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="4"
                                        placeholder="Describe what this category is for...">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                        <i class="fas fa-info-circle"></i> Optional description to help understand the
                                        category purpose.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} style="width: auto;">
                                        <label for="is_active" style="margin: 0; cursor: pointer;">
                                            <i class="fas fa-toggle-on"></i> Active Category
                                        </label>
                                    </div>
                                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                        <i class="fas fa-info-circle"></i> Inactive categories won't be available for new
                                        videos.
                                    </small>
                                </div>

                                <!-- Category Stats -->
                                <div
                                    style="background: var(--bg-secondary); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                                    <h6
                                        style="color: var(--primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-chart-bar"></i> Category Statistics
                                    </h6>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <small style="color: var(--text-muted);">Videos in Category:</small>
                                            <p style="margin: 0; font-weight: bold;">{{ $category->videos_count }}</p>
                                        </div>
                                        <div>
                                            <small style="color: var(--text-muted);">Created:</small>
                                            <p style="margin: 0; font-weight: bold;">
                                                {{ $category->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                                    <a href="{{ route('admin.categories') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Category
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Warning</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-danger mb-2"><i class="fas fa-ban"></i> Deletion Restriction</h6>
                                <small>
                                    <p class="mb-2">This category contains <strong>{{ $category->videos_count }}</strong>
                                        video(s).</p>
                                    @if($category->videos_count > 0)
                                        <p class="mb-0 text-danger"><strong>Cannot be deleted</strong> while videos exist.</p>
                                    @else
                                        <p class="mb-0 text-success">Can be safely deleted.</p>
                                    @endif
                                </small>
                            </div>

                            <hr class="my-3">

                            <div>
                                <h6 class="text-info mb-2"><i class="fas fa-info-circle"></i> Usage</h6>
                                <small>
                                    <p class="mb-0">Categories help interns find relevant training videos.</p>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection