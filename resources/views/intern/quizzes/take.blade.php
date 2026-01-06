@extends('layouts.app')

@section('title', 'Taking Quiz - ' . $quiz->title)

@section('content')
    <div class="quiz-taking-page">
        <div class="quiz-header">
            <div class="quiz-info">
                <h1>{{ $quiz->title }}</h1>
                <div class="quiz-progress">
                    <span class="progress-text">Question {{ $currentQuestionIndex + 1 }} of {{ $questions->count() }}</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ (($currentQuestionIndex + 1) / $questions->count()) * 100 }}%;"></div>
                    </div>
                </div>
            </div>

            @if($quiz->time_limit)
                <div class="quiz-timer">
                    <i class="fas fa-clock"></i>
                    <span id="timer">00:00</span>
                </div>
            @endif
        </div>

        <div class="quiz-content">
            <div class="question-card">
                <div class="question-header">
                    <span class="question-number">Question {{ $currentQuestionIndex + 1 }}</span>
                    <span class="question-points">{{ $currentQuestion->points }} point{{ $currentQuestion->points !== 1 ? 's' : '' }}</span>
                </div>

                <div class="question-text">
                    {{ $currentQuestion->question_text }}
                </div>

                <form method="POST" action="{{ route('intern.quizzes.answer', $attempt) }}" id="answerForm">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                    <input type="hidden" name="next" value="{{ $currentQuestionIndex }}">

                    <div class="options-list">
                        @foreach($currentQuestion->options as $option)
                            <label class="option-item {{ $userAnswer && $userAnswer->selected_option_id == $option->id ? 'selected' : '' }}">
                                <input type="radio" name="selected_option_id" value="{{ $option->id }}"
                                    {{ $userAnswer && $userAnswer->selected_option_id == $option->id ? 'checked' : '' }} required>
                                <span class="option-text">{{ $option->option_text }}</span>
                                <span class="checkmark"></span>
                            </label>
                        @endforeach
                    </div>

                    <div class="question-actions">
                        @if($currentQuestionIndex > 0)
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                        @endif

                        <button type="submit" class="btn btn-primary" id="nextBtn">
                            @if($currentQuestionIndex + 1 >= $questions->count())
                                <i class="fas fa-check"></i> Finish Quiz
                            @else
                                Next Question <i class="fas fa-arrow-right"></i>
                            @endif
                        </button>
                    </div>
                </form>
            </div>

            <div class="quiz-navigation">
                <h4>Questions</h4>
                <div class="question-nav">
                    @foreach($questions as $index => $question)
                        @php
                            $answer = $attempt->answers->where('question_id', $question->id)->first();
                        @endphp
                        <button class="nav-item {{ $index == $currentQuestionIndex ? 'current' : '' }} {{ $answer ? 'answered' : 'unanswered' }}"
                                onclick="goToQuestion({{ $index }})">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .quiz-taking-page {
            min-height: 100vh;
            background: var(--bg-primary);
        }

        .quiz-header {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .quiz-info h1 {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary);
            font-size: 1.5rem;
        }

        .quiz-progress {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .progress-text {
            font-weight: 500;
            color: var(--text-secondary);
        }

        .progress-bar {
            width: 200px;
            height: 8px;
            background: var(--bg-secondary);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .quiz-timer {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
        }

        .quiz-content {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .question-card {
            background: var(--bg-primary);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .question-number {
            font-weight: 600;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .question-points {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .question-text {
            font-size: 1.2rem;
            line-height: 1.6;
            color: var(--text-primary);
            margin-bottom: 2rem;
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 2px solid var(--border-light);
            border-radius: 0.5rem;
            background: var(--bg-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .option-item:hover {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
        }

        .option-item.selected {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }

        .option-item input[type="radio"] {
            display: none;
        }

        .option-text {
            flex: 1;
            color: var(--text-primary);
            font-weight: 500;
        }

        .checkmark {
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid var(--border);
            border-radius: 50%;
            background: white;
            position: relative;
            transition: all 0.2s ease;
        }

        .option-item.selected .checkmark {
            border-color: var(--primary);
            background: var(--primary);
        }

        .option-item.selected .checkmark::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        .question-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .quiz-navigation {
            background: var(--bg-primary);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: fit-content;
            border: 1px solid var(--border);
        }

        .quiz-navigation h4 {
            margin: 0 0 1rem 0;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .question-nav {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .nav-item {
            width: 40px;
            height: 40px;
            border: 2px solid var(--border);
            border-radius: 0.375rem;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-item:hover {
            border-color: var(--primary);
        }

        .nav-item.current {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }

        .nav-item.answered {
            background: rgba(34, 197, 94, 0.1);
            border-color: #16a34a;
            color: #16a34a;
        }

        @media (max-width: 1024px) {
            .quiz-content {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .quiz-navigation {
                order: -1;
            }

            .question-nav {
                grid-template-columns: repeat(10, 1fr);
            }
        }

        @media (max-width: 768px) {
            .quiz-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .quiz-content {
                padding: 1rem;
            }

            .question-card {
                padding: 1.5rem;
            }

            .question-actions {
                flex-direction: column;
            }

            .question-actions .btn {
                width: 100%;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        @if($quiz->time_limit)
            let timeLeft = {{ $quiz->time_limit * 60 }}; // Convert to seconds
            const timerElement = document.getElementById('timer');

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 300) { // 5 minutes warning
                    timerElement.style.color = '#dc2626';
                }

                if (timeLeft <= 0) {
                    // Auto-submit the quiz
                    document.getElementById('answerForm').submit();
                } else {
                    timeLeft--;
                }
            }

            setInterval(updateTimer, 1000);
            updateTimer();
        @endif

        // Handle radio button selection
        document.querySelectorAll('.option-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });

            const radio = item.querySelector('input[type="radio"]');
            if (radio) {
                radio.addEventListener('change', function() {
                    // Update UI when radio changes
                    document.querySelectorAll('.option-item').forEach(opt => opt.classList.remove('selected'));
                    if (this.checked) {
                        this.closest('.option-item').classList.add('selected');
                    }
                });
            }
        });

        function goToQuestion(questionIndex) {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("intern.quizzes.take", $attempt->id) }}';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'question';
            input.value = questionIndex;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        // Handle option selection
        document.querySelectorAll('.option-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.option-item').forEach(opt => opt.classList.remove('selected'));

                // Add selected class to clicked option
                this.classList.add('selected');

                // Check the radio button
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
@endpush