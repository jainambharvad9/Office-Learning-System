@extends('layouts.app')

@section('title', 'Start Quiz - ' . $quiz->title)

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">{{ $quiz->title }}</h1>
                <p class="dashboard-subtitle">Get ready to test your knowledge</p>
            </div>

            <div class="quiz-intro-card">
                <div class="quiz-info">
                    <div class="info-section">
                        <h3>Quiz Overview</h3>
                        @if($quiz->description)
                            <p>{{ $quiz->description }}</p>
                        @endif
                    </div>

                    <div class="quiz-details">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-value">{{ $quiz->questions_count }}</div>
                                <div class="detail-label">Questions</div>
                            </div>
                        </div>

                        @if($quiz->time_limit)
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-value">{{ $quiz->time_limit }}</div>
                                    <div class="detail-label">Minutes</div>
                                </div>
                            </div>
                        @endif

                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-value">{{ $quiz->passing_score }}%</div>
                                <div class="detail-label">Passing Score</div>
                            </div>
                        </div>

                        @if($quiz->category)
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-value">{{ $quiz->category->name }}</div>
                                    <div class="detail-label">Category</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="quiz-instructions">
                    <h3>Instructions</h3>
                    <ul>
                        <li>Read each question carefully before selecting your answer</li>
                        <li>You can only select one answer per question</li>
                        @if($quiz->time_limit)
                            <li>You have {{ $quiz->time_limit }} minutes to complete the quiz</li>
                        @endif
                        <li>Once submitted, you cannot change your answers</li>
                        <li>You need {{ $quiz->passing_score }}% or higher to pass</li>
                    </ul>
                </div>

                <div class="quiz-actions">
                    <form method="POST" action="{{ route('intern.quizzes.start', $quiz) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-play"></i> Start Quiz
                        </button>
                    </form>
                    <a href="{{ route('intern.quizzes.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to Quizzes
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .quiz-intro-card {
            max-width: 800px;
            margin: 2rem auto;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .quiz-info {
            margin-bottom: 2rem;
        }

        .info-section h3 {
            margin: 0 0 1rem 0;
            color: var(--text-primary);
            font-size: 1.2rem;
        }

        .info-section p {
            margin: 0;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .quiz-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: 0.5rem;
            border: 1px solid var(--border-light);
        }

        .detail-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .detail-content {
            text-align: center;
        }

        .detail-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quiz-instructions {
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .quiz-instructions h3 {
            margin: 0 0 1rem 0;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .quiz-instructions ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .quiz-instructions li {
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .quiz-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .quiz-intro-card {
                margin: 1rem;
                padding: 1.5rem;
            }

            .quiz-details {
                grid-template-columns: repeat(2, 1fr);
            }

            .detail-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
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