@extends('layouts.app')

@section('title', 'Edit Question - ' . $question->quiz->title)

@section('content')
    <div class="dashboard">
        <div class="container-fluid px-4 py-4">
            <div class="mb-4">
                <h1 class="h2 fw-bold mb-2">Edit Question</h1>
                <p class="text-muted">Update question for "{{ $question->quiz->title }}"</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <h3 class="h5 mb-0">Question Details</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.questions.update', $question) }}" id="questionForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="question_text" class="form-label fw-semibold">Question Text *</label>
                            <textarea id="question_text" name="question_text" class="form-control" rows="3"
                                required>{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="question_type" class="form-label fw-semibold">Question Type *</label>
                                <select id="question_type" name="question_type" class="form-select" required>
                                    <option value="multiple_choice" {{ old('question_type', $question->question_type) == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice
                                    </option>
                                    <option value="true_false" {{ old('question_type', $question->question_type) == 'true_false' ? 'selected' : '' }}>
                                        True/False</option>
                                </select>
                                @error('question_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="points" class="form-label fw-semibold">Points *</label>
                                <input type="number" id="points" name="points" class="form-control"
                                    value="{{ old('points', $question->points) }}" min="1" required>
                                @error('points')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="optionsSection" class="mt-4">
                            <h4 class="h5 fw-semibold mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-list"></i>
                                Answer Options
                            </h4>

                            <div id="optionsContainer" class="mb-3">
                                <!-- Options will be loaded here dynamically -->
                            </div>

                            <button type="button" id="addOptionBtn" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Question
                            </button>
                            <a href="{{ route('admin.questions.index', $question->quiz) }}"
                                class="btn btn-outline-secondary">
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
        .option-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .option-input {
            flex: 1;
        }

        .option-radio {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 100px;
        }

        .option-radio input[type="radio"] {
            margin: 0;
            cursor: pointer;
        }

        .option-radio label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }

        .remove-option {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }

        .remove-option:hover {
            background: rgba(220, 53, 69, 0.1);
            color: #bb2d3b;
        }

        #trueFalseOptions {
            display: none;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const questionTypeSelect = document.getElementById('question_type');
            const optionsSection = document.getElementById('optionsSection');
            const optionsContainer = document.getElementById('optionsContainer');
            const addOptionBtn = document.getElementById('addOptionBtn');
            let optionCount = 0;

            // Existing options from database
            const existingOptions = @json($question->options);
            const correctOption = existingOptions.find(opt => opt.is_correct)?.id;

            // True/False options
            const trueFalseOptions = `
                        <div id="trueFalseOptions">
                            <div class="option-item">
                                <div class="option-radio">
                                    <input type="radio" name="correct_option" value="1" class="form-check-input" ${correctOption == 1 ? 'checked' : ''} required>
                                    <label class="form-check-label">True</label>
                                </div>
                            </div>
                            <div class="option-item">
                                <div class="option-radio">
                                    <input type="radio" name="correct_option" value="2" class="form-check-input" ${correctOption == 2 ? 'checked' : ''} required>
                                    <label class="form-check-label">False</label>
                                </div>
                            </div>
                        </div>
                    `;

            function updateOptionsDisplay() {
                const questionType = questionTypeSelect.value;

                if (questionType === 'true_false') {
                    optionsContainer.innerHTML = trueFalseOptions;
                    addOptionBtn.style.display = 'none';
                    document.getElementById('trueFalseOptions').style.display = 'block';
                } else {
                    optionsContainer.innerHTML = '';
                    addOptionBtn.style.display = 'inline-block';
                    optionCount = 0;

                    // Load existing options
                    if (existingOptions.length > 0) {
                        existingOptions.forEach(option => {
                            addOption(option);
                        });
                    } else {
                        addOption();
                        addOption();
                    }
                }
            }

            function addOption(existingOption = null) {
                optionCount++;
                const optionId = existingOption ? existingOption.id : optionCount;
                const optionText = existingOption ? existingOption.option_text : '';
                const isCorrect = existingOption ? existingOption.is_correct : false;

                const optionHtml = `
                            <div class="option-item" data-option-id="${optionCount}">
                                <div class="option-input">
                                    <input type="hidden" name="options[${optionCount}][id]" value="${existingOption ? existingOption.id : ''}">
                                    <input type="text" name="options[${optionCount}][text]" class="form-control" placeholder="Option ${optionCount}" value="${optionText}" required>
                                </div>
                                <div class="option-radio">
                                    <input type="radio" name="correct_option" value="${optionCount}" class="form-check-input" ${isCorrect ? 'checked' : ''} required>
                                    <label class="form-check-label">Correct</label>
                                </div>
                                <button type="button" class="remove-option" onclick="removeOption(${optionCount})" title="Remove option">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
            }

            window.removeOption = function (optionId) {
                const optionElement = document.querySelector(`[data-option-id="${optionId}"]`);
                if (optionElement) {
                    optionElement.remove();
                }
            };

            addOptionBtn.addEventListener('click', () => addOption());
            questionTypeSelect.addEventListener('change', updateOptionsDisplay);

            // Initialize
            updateOptionsDisplay();
        });
    </script>
@endpush