@extends('layouts.app')

@section('title', 'Create Category - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Create New Category</h1>
                <p class="dashboard-subtitle">Add a new video category for better organization.</p>
            </div>

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('admin.categories') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>

            <!-- Create Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3
                                style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-plus"></i>
                                Category Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.categories.store') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag"></i> Category Name <span style="color: var(--error);">*</span>
                                    </label>
                                    <input type="text" class="form-input @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}" required
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
                                        placeholder="Describe what this category is for...">{{ old('description') }}</textarea>
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
                                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: auto;">
                                        <label for="is_active" style="margin: 0; cursor: pointer;">
                                            <i class="fas fa-toggle-on"></i> Active Category
                                        </label>
                                    </div>
                                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                        <i class="fas fa-info-circle"></i> Inactive categories won't be available for new
                                        videos.
                                    </small>
                                </div>

                                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                                    <a href="{{ route('admin.categories') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Category
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary mb-2"><i class="fas fa-info-circle"></i> Category Examples</h6>
                                <ul class="mb-0 small">
                                    <li><strong>GSTR</strong> - GST Return Filing</li>
                                    <li><strong>ITR</strong> - Income Tax Returns</li>
                                    <li><strong>TDS</strong> - Tax Deducted at Source</li>
                                    <li><strong>Accounting</strong> - General Accounting</li>
                                    <li><strong>Compliance</strong> - Legal Compliance</li>
                                </ul>
                            </div>

                            <hr class="my-3">

                            <div>
                                <h6 class="text-warning mb-2"><i class="fas fa-exclamation-triangle"></i> Important</h6>
                                <small>
                                    <p class="mb-2">Categories help organize videos for interns.</p>
                                    <p class="mb-0">You can assign videos to categories during upload.</p>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection