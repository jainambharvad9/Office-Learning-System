@extends('layouts.app')

@section('title', 'Manage Quizzes - Admin')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Quiz Management</h1>
                <p class="dashboard-subtitle">Create and manage quizzes for interns</p>
            </div>

            <div class="section-header" style="margin-bottom: 2rem;">
                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Quiz
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-brain"></i>
                        All Quizzes
                    </h3>
                </div>
                <div class="card-body">
                    @if($quizzes->count() > 0)
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--border);">
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Title</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Category</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Questions</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Attempts</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Status</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizzes as $quiz)
                                        <tr style="border-bottom: 1px solid var(--border-light);">
                                            <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                                {{ $quiz->title }}
                                                @if($quiz->description)
                                                    <br><small
                                                        style="color: var(--text-muted);">{{ Str::limit($quiz->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem; color: var(--text-secondary);">
                                                {{ $quiz->category ? $quiz->category->name : 'No Category' }}
                                            </td>
                                            <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                                {{ $quiz->questions_count }}
                                            </td>
                                            <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                                {{ $quiz->attempts_count }}
                                            </td>
                                            <td style="padding: 0.75rem;">
                                                <span
                                                    class="status-badge {{ $quiz->is_active ? 'status-completed' : 'status-not-started' }}">
                                                    {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.75rem;">
                                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                    <a href="{{ route('admin.questions.index', $quiz) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-question"></i> Questions
                                                    </a>
                                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}"
                                                        style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 1rem;">
                            {{ $quizzes->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-brain" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No quizzes created yet.</p>
                            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Your First Quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .status-not-started {
            background: rgba(156, 163, 175, 0.1);
            color: #6b7280;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endsection