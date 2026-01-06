@extends('layouts.app')

@section('title', 'Quizzes - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Knowledge Assessment</h1>
                <p class="dashboard-subtitle">Test your understanding with interactive quizzes</p>
            </div>

            <div class="quiz-grid">
                @forelse($quizzes as $quiz)
                    <div class="quiz-card">
                        <div class="quiz-header">
                            <div class="quiz-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div class="quiz-status">
                                @if($quiz->latest_attempt && $quiz->latest_attempt->is_completed)
                                    @if($quiz->latest_attempt->is_passed)
                                        <span class="status-badge status-passed">
                                            <i class="fas fa-check-circle"></i> Passed
                                        </span>
                                    @else
                                        <span class="status-badge status-failed">
                                            <i class="fas fa-times-circle"></i> Failed
                                        </span>
                                    @endif
                                @elseif($quiz->latest_attempt && !$quiz->latest_attempt->is_completed)
                                    <span class="status-badge status-in-progress">
                                        <i class="fas fa-clock"></i> In Progress
                                    </span>
                                @else
                                    <span class="status-badge status-not-started">
                                        <i class="fas fa-play"></i> Not Started
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="quiz-content">
                            <h3 class="quiz-title">{{ $quiz->title }}</h3>
                            @if($quiz->description)
                                <p class="quiz-description">{{ Str::limit($quiz->description, 100) }}</p>
                            @endif

                            <div class="quiz-meta">
                                <div class="meta-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span>{{ $quiz->questions_count }} Questions</span>
                                </div>
                                @if($quiz->time_limit)
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $quiz->time_limit }} Minutes</span>
                                    </div>
                                @endif
                                @if($quiz->category)
                                    <div class="meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span>{{ $quiz->category->name }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($quiz->latest_attempt && $quiz->latest_attempt->is_completed)
                                <div class="quiz-result">
                                    <div class="result-score">
                                        <span class="score-value">{{ $quiz->latest_attempt->percentage }}%</span>
                                        <span class="score-label">Your Score</span>
                                    </div>
                                    <div class="result-details">
                                        <span>{{ $quiz->latest_attempt->correct_answers }}/{{ $quiz->latest_attempt->total_questions }}
                                            Correct</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="quiz-actions">
                            @if($quiz->latest_attempt && !$quiz->latest_attempt->is_completed)
                                <a href="{{ route('intern.quizzes.take', $quiz->latest_attempt->id) }}" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Continue Quiz
                                </a>
                            @elseif($quiz->can_retake)
                                <form method="POST" action="{{ route('intern.quizzes.start', $quiz) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Start Quiz
                                    </button>
                                </form>
                            @else
                                <span class="btn btn-disabled">
                                    <i class="fas fa-check"></i> Completed
                                </span>
                            @endif

                            @if($quiz->latest_attempt && $quiz->latest_attempt->is_completed)
                                <a href="{{ route('intern.quizzes.result', $quiz->latest_attempt->id) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-chart-bar"></i> View Results
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3>No Quizzes Available</h3>
                        <p>Check back later for new assessment opportunities!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .quiz-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .quiz-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .quiz-icon {
            width: 3rem;
            height: 3rem;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-passed {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .status-failed {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .status-in-progress {
            background: rgba(251, 191, 36, 0.1);
            color: #d97706;
        }

        .status-not-started {
            background: rgba(156, 163, 175, 0.1);
            color: #6b7280;
        }

        .quiz-title {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .quiz-description {
            margin: 0 0 1rem 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .quiz-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .quiz-result {
            background: var(--bg-secondary);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .result-score {
            margin-bottom: 0.5rem;
        }

        .score-value {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .score-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .result-details {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .quiz-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-disabled {
            background: var(--text-muted);
            color: white;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .quiz-grid {
                grid-template-columns: 1fr;
            }

            .quiz-actions {
                flex-direction: column;
            }

            .quiz-actions .btn {
                width: 100%;
            }
        }
    </style>
@endsection