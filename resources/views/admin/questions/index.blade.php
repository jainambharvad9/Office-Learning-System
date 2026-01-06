@extends('layouts.app')

@section('title', 'Manage Questions - ' . $quiz->title)

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Questions for "{{ $quiz->title }}"</h1>
                <p class="dashboard-subtitle">Manage quiz questions and answers</p>
            </div>

            <div class="section-header" style="margin-bottom: 2rem;">
                <a href="{{ route('admin.questions.create', $quiz) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Question
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Quizzes
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-question-circle"></i>
                        Questions ({{ $questions->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($questions->count() > 0)
                        <div class="questions-list">
                            @foreach($questions as $index => $question)
                                <div class="question-item">
                                    <div class="question-header">
                                        <div class="question-number">
                                            <span class="question-badge">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="question-content">
                                            <h4 class="question-text">{{ $question->question_text }}</h4>
                                            <div class="question-meta">
                                                <span class="question-type">
                                                    <i class="fas fa-tag"></i>
                                                    {{ $question->question_type === 'multiple_choice' ? 'Multiple Choice' : 'True/False' }}
                                                </span>
                                                <span class="question-points">
                                                    <i class="fas fa-star"></i>
                                                    {{ $question->points }} point{{ $question->points !== 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="question-actions">
                                            <a href="{{ route('admin.questions.edit', $question) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this question?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="question-options">
                                        <h5>Options:</h5>
                                        <div class="options-list">
                                            @foreach($question->options as $option)
                                                <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                                    <span class="option-text">{{ $option->option_text }}</span>
                                                    @if($option->is_correct)
                                                        <span class="correct-badge">
                                                            <i class="fas fa-check"></i> Correct
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-question-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No questions added yet.</p>
                            <a href="{{ route('admin.questions.create', $quiz) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Your First Question
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
        .questions-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .question-item {
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 1.5rem;
            background: var(--bg-primary);
        }

        .question-header {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .question-number {
            flex-shrink: 0;
        }

        .question-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .question-content {
            flex: 1;
        }

        .question-text {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary);
            font-size: 1.1rem;
            line-height: 1.4;
        }

        .question-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .question-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .question-options h5 {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .option-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            background: var(--bg-secondary);
            border-radius: 0.375rem;
            border: 1px solid var(--border-light);
        }

        .option-item.correct {
            background: rgba(34, 197, 94, 0.1);
            border-color: #16a34a;
        }

        .option-text {
            color: var(--text-primary);
        }

        .correct-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: #16a34a;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endsection