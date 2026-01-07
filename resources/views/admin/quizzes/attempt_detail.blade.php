@extends('layouts.app')

@section('title', 'Attempt Details - ' . $attempt->user->name)

@section('content')
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}">

    <div class="container" style="padding: 2rem;">
        <!-- Page Header -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-file-alt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Attempt Details
                    </h1>
                    <p style="color: var(--text-secondary); margin: 0;">{{ $attempt->quiz->title }}</p>
                </div>
                <a href="{{ route('admin.quizzes.results', $attempt->quiz_id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Results
                </a>
            </div>
        </div>

        <!-- Intern Info Card -->
        <div class="card"
            style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                <div
                    style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">
                    {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                </div>
                <div style="flex: 1;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                        {{ $attempt->user->name }}</h2>
                    <p style="color: var(--text-secondary); margin: 0;">{{ $attempt->user->email }}</p>
                </div>
                <div style="text-align: right;">
                    @php
                        $isPassed = $attempt->percentage >= $attempt->quiz->passing_score;
                    @endphp
                    @if($isPassed)
                        <span
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; background: rgba(16, 185, 129, 0.15); color: var(--success); border-radius: 25px; font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-check-circle"></i> PASSED
                        </span>
                    @else
                        <span
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; background: rgba(239, 68, 68, 0.15); color: var(--error); border-radius: 25px; font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-times-circle"></i> FAILED
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Score Summary -->
        <div class="stats-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: {{ $isPassed ? 'var(--success)' : 'var(--error)' }};">
                    {{ round($attempt->percentage) }}%</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;">Final Score</div>
            </div>

            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $attempt->correct_answers }} /
                    {{ $attempt->total_questions }}</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;">Correct Answers</div>
            </div>

            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem; text-align: center;">
                @php
                    $minutes = floor(($attempt->time_taken ?? 0) / 60);
                    $seconds = ($attempt->time_taken ?? 0) % 60;
                @endphp
                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">
                    {{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;">Time Taken</div>
            </div>

            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem; text-align: center;">
                <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary);">
                    {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : $attempt->created_at->format('M d, Y') }}
                </div>
                <div style="font-size: 0.9rem; color: var(--text-muted);">
                    {{ $attempt->completed_at ? $attempt->completed_at->format('h:i A') : $attempt->created_at->format('h:i A') }}
                </div>
                <div style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">Completed</div>
            </div>
        </div>

        <!-- Question Review -->
        <div class="card"
            style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); background: var(--surface);">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin: 0;">
                    <i class="fas fa-question-circle" style="color: var(--primary); margin-right: 0.5rem;"></i>
                    Question Review
                </h3>
            </div>

            <div style="padding: 1.5rem;">
                @foreach($attempt->answers as $index => $answer)
                    @php
                        $question = $answer->question;
                        $isCorrect = $answer->is_correct;
                        $selectedOption = $answer->selectedOption;
                        $correctOption = $question->options->where('is_correct', true)->first();
                    @endphp
                    <div
                        style="margin-bottom: 1.5rem; padding: 1.25rem; background: var(--surface); border-radius: var(--radius); border-left: 4px solid {{ $isCorrect ? 'var(--success)' : 'var(--error)' }};">
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <h4 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin: 0;">
                                Q{{ $index + 1 }}. {{ $question->question_text }}
                            </h4>
                            @if($isCorrect)
                                <span
                                    style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.6rem; background: rgba(16, 185, 129, 0.15); color: var(--success); border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-check"></i> Correct
                                </span>
                            @else
                                <span
                                    style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.6rem; background: rgba(239, 68, 68, 0.15); color: var(--error); border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-times"></i> Wrong
                                </span>
                            @endif
                        </div>

                        <div style="display: grid; gap: 0.5rem;">
                            @foreach($question->options as $option)
                                @php
                                    $isSelected = $selectedOption && $selectedOption->id == $option->id;
                                    $isCorrectOption = $option->is_correct;

                                    $bgColor = 'transparent';
                                    $borderColor = 'var(--border)';
                                    $textColor = 'var(--text-secondary)';

                                    if ($isSelected && $isCorrect) {
                                        $bgColor = 'rgba(16, 185, 129, 0.1)';
                                        $borderColor = 'var(--success)';
                                        $textColor = 'var(--success)';
                                    } elseif ($isSelected && !$isCorrect) {
                                        $bgColor = 'rgba(239, 68, 68, 0.1)';
                                        $borderColor = 'var(--error)';
                                        $textColor = 'var(--error)';
                                    } elseif ($isCorrectOption && !$isCorrect) {
                                        $bgColor = 'rgba(16, 185, 129, 0.1)';
                                        $borderColor = 'var(--success)';
                                        $textColor = 'var(--success)';
                                    }
                                @endphp
                                <div
                                    style="padding: 0.6rem 1rem; background: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 0.75rem;">
                                    @if($isSelected)
                                        @if($isCorrect)
                                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                        @else
                                            <i class="fas fa-times-circle" style="color: var(--error);"></i>
                                        @endif
                                    @elseif($isCorrectOption && !$isCorrect)
                                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                    @else
                                        <i class="far fa-circle" style="color: var(--text-muted);"></i>
                                    @endif
                                    <span style="color: {{ $textColor }}; font-size: 0.9rem;">{{ $option->option_text }}</span>
                                    @if($isSelected)
                                        <span
                                            style="margin-left: auto; font-size: 0.75rem; color: {{ $textColor }}; font-weight: 500;">(Selected)</span>
                                    @endif
                                    @if($isCorrectOption && !$isSelected)
                                        <span
                                            style="margin-left: auto; font-size: 0.75rem; color: var(--success); font-weight: 500;">(Correct
                                            Answer)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection