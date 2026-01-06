<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of available quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::where('is_active', true)
            ->with(['category', 'creator'])
            ->withCount('questions')
            ->latest()
            ->get()
            ->map(function ($quiz) {
                $attempt = QuizAttempt::where('user_id', Auth::id())
                    ->where('quiz_id', $quiz->id)
                    ->latest()
                    ->first();

                $quiz->latest_attempt = $attempt;
                $quiz->can_retake = !$attempt || $attempt->is_completed;
                return $quiz;
            });

        return view('intern.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the quiz start page.
     */
    public function show(Quiz $quiz)
    {
        if (!$quiz->is_active) {
            abort(404);
        }

        $existingAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('is_completed', false)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('intern.quizzes.take', $existingAttempt->id);
        }

        $quiz->load(['questions.options' => function ($query) {
            $query->orderBy('order');
        }]);

        return view('intern.quizzes.show', compact('quiz'));
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(Quiz $quiz)
    {
        if (!$quiz->is_active) {
            abort(404);
        }

        // // Check if user already has a completed attempt and quiz doesn't allow retakes
        // $existingCompletedAttempt = QuizAttempt::where('user_id', Auth::id())
        //     ->where('quiz_id', $quiz->id)
        //     ->where('is_completed', true)
        //     ->exists();

        // if ($existingCompletedAttempt) {
        //     return redirect()->route('intern.quizzes.index')
        //         ->with('info', 'You have already completed this quiz.');
        // }

        // Check for existing incomplete attempt
        $existingAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('is_completed', false)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('intern.quizzes.take', $existingAttempt->id);
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'total_questions' => $quiz->questions()->count(),
            'score' => 0,
            'correct_answers' => 0,
            'is_completed' => false,
            'started_at' => now()
        ]);

        return redirect()->route('intern.quizzes.take', $attempt->id);
    }

    /**
     * Take the quiz.
     */
    public function take(QuizAttempt $attempt)
    {
        // Ensure user owns this attempt
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->is_completed) {
            return redirect()->route('intern.quizzes.result', $attempt->id);
        }

        $quiz = $attempt->quiz->load(['questions.options' => function ($query) {
            $query->orderBy('order');
        }]);

        $questions = $quiz->questions;
        $currentQuestionIndex = request('question', 0);

        if ($currentQuestionIndex >= $questions->count()) {
            return $this->completeQuiz($attempt);
        }

        $currentQuestion = $questions[$currentQuestionIndex];
        $userAnswer = QuizAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $currentQuestion->id)
            ->first();

        return view('intern.quizzes.take', compact(
            'attempt',
            'quiz',
            'questions',
            'currentQuestion',
            'currentQuestionIndex',
            'userAnswer'
        ));
    }

    /**
     * Store quiz answer.
     */
    public function answer(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->is_completed) {
            abort(403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'required|exists:question_options,id'
        ]);

        $question = $attempt->quiz->questions()->findOrFail($validated['question_id']);
        $selectedOption = $question->options()->findOrFail($validated['selected_option_id']);

        // Save or update answer
        QuizAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id']
            ],
            [
                'selected_option_id' => $validated['selected_option_id'],
                'is_correct' => $selectedOption->is_correct
            ]
        );

        $nextQuestion = request('next', 0) + 1;

        if ($nextQuestion >= $attempt->total_questions) {
            return $this->completeQuiz($attempt);
        }

        return redirect()->route('intern.quizzes.take', [
            $attempt->id,
            'question' => $nextQuestion
        ]);
    }

    /**
     * Complete the quiz and calculate results.
     */
    private function completeQuiz(QuizAttempt $attempt)
    {
        $answers = $attempt->answers;
        $correctAnswers = $answers->where('is_correct', true)->count();
        $totalScore = $answers->sum(function ($answer) {
            return $answer->is_correct ? $answer->question->points : 0;
        });

        // Calculate time taken in seconds
        $timeTaken = null;
        if ($attempt->started_at) {
            $timeTaken = (int) now()->diffInSeconds($attempt->started_at);
        }

        $attempt->update([
            'correct_answers' => $correctAnswers,
            'score' => $totalScore,
            'is_completed' => true,
            'time_taken' => $timeTaken,
            'completed_at' => now()
        ]);

        return redirect()->route('intern.quizzes.result', $attempt->id);
    }

    /**
     * Show quiz result.
     */
    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load(['quiz', 'answers.question.options', 'answers.selectedOption']);

        return view('intern.quizzes.result', compact('attempt'));
    }
}