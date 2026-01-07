@extends('layouts.app')

@section('title', 'Add Question - ' . $quiz->title)

@section('content')
    <div class="dashboard">
        <div class="container-fluid px-4 py-4">
            <div class="mb-4">
                <h1 class="h2 fw-bold mb-2">Add Question</h1>
                <p class="text-muted">Create a new question for "{{ $quiz->title }}"</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <h3 class="h5 mb-0">Question Details</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.questions.store', $quiz) }}" id="questionForm">
                        @csrf

                        <div class="mb-3">
                            <label for="question_text" class="form-label fw-semibold">Question Text *</label>
                            <textarea id="question_text" name="question_text" class="form-control" rows="3"
                                required>{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="question_type" class="form-label fw-semibold">Question Type *</label>
                                <select id="question_type" name="question_type" class="form-select" required>
                                    <option value="multiple_choice" {{ old('question_type', 'multiple_choice') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                    <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>
                                        True/False</option>
                                </select>
                                @error('question_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="points" class="form-label fw-semibold">Points *</label>
                                <input type="number" id="points" name="points" class="form-control"
                                    value="{{ old('points', 1) }}" min="1" required>
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
                                <!-- Options will be added here dynamically -->
                            </div>

                            <button type="button" id="addOptionBtn" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> Save Question
                            </button>
                            <button type="submit" class="btn btn-success" id="saveAddBtn">
                                <i class="fas fa-plus"></i> Save & Add Another
                            </button>
                            <a href="{{ route('admin.questions.index', $quiz) }}" class="btn btn-outline-secondary">
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
        /* Dark mode support for text-muted */
        .text-muted {
            color: var(--text-secondary) !important;
        }

        /* Dark mode support for form labels */
        .form-label,
        label.form-label {
            color: var(--text-primary) !important;
        }

        /* Dark mode support for section headings */
        .h4.fw-semibold,
        .h5.fw-semibold {
            color: var(--text-primary) !important;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            color: var(--text-primary);
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
            color: var(--text-primary);
        }

        .remove-option {
            background: none;
            border: none;
            color: var(--error);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }

        .remove-option:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
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
            const questionForm = document.getElementById('questionForm');
            const saveBtn = document.getElementById('saveBtn');
            const saveAddBtn = document.getElementById('saveAddBtn');
            let optionCount = 0;

            // True/False options
            const trueFalseOptions = `
                        <div id="trueFalseOptions">
                            <div class="option-item">
                                <div class="option-radio">
                                    <input type="radio" name="correct_option" value="1" class="form-check-input" required>
                                    <label class="form-check-label">True</label>
                                </div>
                            </div>
                            <div class="option-item">
                                <div class="option-radio">
                                    <input type="radio" name="correct_option" value="2" class="form-check-input" required>
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
                    addOption();
                    addOption();
                }
            }

            function addOption() {
                optionCount++;
                const optionHtml = `
                            <div class="option-item" data-option-id="${optionCount}">
                                <div class="option-input">
                                    <input type="text" name="options[${optionCount}][text]" class="form-control" placeholder="Option ${optionCount}" required>
                                </div>
                                <div class="option-radio">
                                    <input type="radio" name="correct_option" value="${optionCount}" class="form-check-input" required>
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

            function resetForm() {
                questionForm.reset();
                document.getElementById('question_text').value = '';
                document.getElementById('question_type').value = 'multiple_choice';
                document.getElementById('points').value = 1;
                optionCount = 0;
                updateOptionsDisplay();
            }

            // Handle Save & Add Another button
            saveAddBtn.addEventListener('click', function (e) {
                e.preventDefault();

                // Submit via AJAX to avoid page reload
                const formData = new FormData(questionForm);

                fetch(questionForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                <i class="fas fa-check-circle"></i> Question saved successfully! Adding new question...
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.querySelector('.card').insertAdjacentElement('beforebegin', alertDiv);

                            // Reset form for next question
                            setTimeout(() => {
                                resetForm();
                                alertDiv.remove();
                            }, 1500);
                        } else {
                            alert('Error saving question');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error saving question');
                    });
            });

            addOptionBtn.addEventListener('click', addOption);
            questionTypeSelect.addEventListener('change', updateOptionsDisplay);

            // Initialize
            updateOptionsDisplay();
        });
    </script>
@endpush