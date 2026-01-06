@extends('layouts.app')

@section('title', 'Quiz Results - ' . $attempt->quiz->title)

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Quiz Results</h1>
                <p class="dashboard-subtitle">{{ $attempt->quiz->title }}</p>
            </div>

            <div class="result-summary">
                <div class="result-card {{ $attempt->is_passed ? 'passed' : 'failed' }}">
                    <div class="result-icon">
                        @if($attempt->is_passed)
                            <i class="fas fa-trophy"></i>
                        @else
                            <i class="fas fa-times-circle"></i>
                        @endif
                    </div>

                    <div class="result-content">
                        <h2 class="result-status">{{ $attempt->is_passed ? 'Congratulations!' : 'Try Again' }}</h2>
                        <div class="result-score">
                            <span class="score-value">{{ $attempt->percentage }}%</span>
                            <span class="score-label">Your Score</span>
                        </div>
                        <div class="result-details">
                            <span>{{ $attempt->correct_answers }}/{{ $attempt->total_questions }} Questions Correct</span>
                        </div>
                    </div>
                </div>

                <div class="result-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">
                                @if($attempt->time_taken)
                                    {{ floor($attempt->time_taken / 60) }}:{{ str_pad($attempt->time_taken % 60, 2, '0', STR_PAD_LEFT) }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="stat-label">Time Taken</div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $attempt->completed_at->format('M j, Y') }}</div>
                            <div class="stat-label">Completed On</div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-target"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $attempt->quiz->passing_score }}%</div>
                            <div class="stat-label">Passing Score</div>
                        </div>
                    </div>

                    @if($attempt->quiz->time_limit)
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-hourglass"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ $attempt->quiz->time_limit }} min</div>
                                <div class="stat-label">Quiz Time Limit</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-list"></i>
                        Question Review
                    </h3>
                </div>
                <div class="card-body">
                    <div class="questions-review">
                        @forelse($attempt->answers->sortBy('question.order') as $index => $answer)
                            @if($answer->question)
                                <div class="question-review-item {{ $answer->is_correct ? 'correct' : 'incorrect' }}">
                                    <div class="question-header">
                                        <span class="question-number">{{ $index + 1 }}</span>
                                        <div class="question-status">
                                            @if($answer->is_correct)
                                                <span class="status-correct">
                                                    <i class="fas fa-check"></i> Correct
                                                </span>
                                            @else
                                                <span class="status-incorrect">
                                                    <i class="fas fa-times"></i> Incorrect
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="question-text">
                                        {{ $answer->question->question_text }}
                                    </div>

                                    <div class="question-options">
                                        @foreach($answer->question->options as $option)
                                            <div
                                                class="option-review {{ $option->is_correct ? 'correct-answer' : '' }} {{ $answer->selected_option_id == $option->id ? 'user-selected' : '' }}">
                                                <span class="option-text">{{ $option->option_text }}</span>
                                                @if($option->is_correct)
                                                    <span class="correct-indicator">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @endif
                                                @if($answer->selected_option_id == $option->id && !$option->is_correct)
                                                    <span class="wrong-indicator">
                                                        <i class="fas fa-times"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No questions to review.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="result-actions">
                <a href="{{ route('intern.quizzes.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Quizzes
                </a>
                @if($attempt->quiz->is_active)
                    <form method="POST" action="{{ route('intern.quizzes.start', $attempt->quiz) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-redo"></i> Retake Quiz
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .result-summary {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .result-card {
            background: var(--bg-primary);
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid;
        }

        .result-card.passed {
            border-color: #16a34a;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
        }

        .result-card.failed {
            border-color: #dc2626;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
        }

        .result-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .result-card.passed .result-icon {
            color: #16a34a;
        }

        .result-card.failed .result-icon {
            color: #dc2626;
        }

        .result-status {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .result-score {
            margin-bottom: 1rem;
        }

        .score-value {
            display: block;
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .score-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .result-details {
            font-size: 1rem;
            color: var(--text-muted);
        }

        .result-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .stat-item {
            background: var(--bg-primary);
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--border);
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .questions-review {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .question-review-item {
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 1.5rem;
            background: var(--bg-primary);
        }

        .question-review-item.correct {
            border-color: #16a34a;
            background: rgba(34, 197, 94, 0.05);
        }

        .question-review-item.incorrect {
            border-color: #dc2626;
            background: rgba(239, 68, 68, 0.05);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .question-number {
            background: var(--primary);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .question-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-correct {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-incorrect {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .question-text {
            font-size: 1.1rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .question-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .option-review {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border-light);
            background: var(--bg-secondary);
            position: relative;
            color: var(--text-primary);
        }

        .option-review.correct-answer {
            background: rgba(34, 197, 94, 0.1);
            border-color: #16a34a;
        }

        .option-review.user-selected {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }

        .correct-indicator,
        .wrong-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            font-size: 0.8rem;
        }

        .correct-indicator {
            background: #16a34a;
            color: white;
        }

        .wrong-indicator {
            background: #dc2626;
            color: white;
        }

        .result-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .result-summary {
                grid-template-columns: 1fr;
            }

            .result-stats {
                flex-direction: row;
                gap: 0.5rem;
            }

            .stat-item {
                flex: 1;
                padding: 1rem;
            }

            .stat-content {
                text-align: left;
            }

            .result-actions {
                flex-direction: column;
            }

            .result-actions .btn {
                width: 100%;
            }
        }
    </style>
@endsection