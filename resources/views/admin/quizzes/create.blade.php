@extends('layouts.app')

@section('title', 'Create Quiz - Admin')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Create New Quiz</h1>
                <p class="dashboard-subtitle">Set up a new quiz for interns</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i>
                        Quiz Details
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.quizzes.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="title" class="form-label">Quiz Title *</label>
                            <input type="text" id="title" name="title" class="form-input"
                                value="{{ old('title') }}" required>
                            @error('title')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-input" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id" class="form-label">Category</label>
                            <select id="category_id" name="category_id" class="form-input">
                                <option value="">No Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                <input type="number" id="time_limit" name="time_limit" class="form-input"
                                    value="{{ old('time_limit') }}" min="1">
                                @error('time_limit')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                                <small style="color: var(--text-muted);">Leave empty for no time limit</small>
                            </div>

                            <div class="form-group">
                                <label for="passing_score" class="form-label">Passing Score (%)</label>
                                <input type="number" id="passing_score" name="passing_score" class="form-input"
                                    value="{{ old('passing_score', 70) }}" min="0" max="100" required>
                                @error('passing_score')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-checkbox">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                Active (visible to interns)
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Quiz
                            </button>
                            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-checkbox input[type="checkbox"] {
            margin: 0;
        }

        .checkmark {
            width: 1rem;
            height: 1rem;
            border: 2px solid var(--border);
            border-radius: 3px;
            background: var(--bg-primary);
            position: relative;
            transition: all 0.2s;
        }

        .form-checkbox input[type="checkbox"]:checked + .checkmark {
            background: var(--primary);
            border-color: var(--primary);
        }

        .form-checkbox input[type="checkbox"]:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: -2px;
            left: 1px;
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
@endsection