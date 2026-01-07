<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = Quiz::with(['category', 'creator'])
            ->withCount('questions')
            ->withCount('attempts')
            ->latest()
            ->paginate(10);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = VideoCategory::all();
        return view('admin.quizzes.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:video_categories,id',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $validated['created_by'] = Auth::id();

        Quiz::create($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.options', 'category', 'creator', 'attempts.user']);

        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        $categories = VideoCategory::all();
        return view('admin.quizzes.edit', compact('quiz', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:video_categories,id',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully!');
    }

    /**
     * Display quiz results with all intern attempts.
     */
    public function results(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('is_completed', true)
            ->with('user')
            ->latest()
            ->paginate(15);

        $totalAttempts = $attempts->total();

        // Calculate passed/failed counts
        $allAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('is_completed', true)
            ->get();

        $passedCount = $allAttempts->filter(function ($attempt) use ($quiz) {
            return $attempt->percentage >= $quiz->passing_score;
        })->count();

        $failedCount = $totalAttempts - $passedCount;
        $averageScore = $allAttempts->count() > 0 ? round($allAttempts->avg('score'), 1) : 0;
        $passRate = $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100, 1) : 0;

        return view('admin.quizzes.results', compact(
            'quiz',
            'attempts',
            'totalAttempts',
            'passedCount',
            'failedCount',
            'averageScore',
            'passRate'
        ));
    }

    /**
     * Display detailed view of a specific attempt.
     */
    public function attemptDetail(QuizAttempt $attempt)
    {
        $attempt->load(['user', 'quiz', 'answers.question.options', 'answers.selectedOption']);

        return view('admin.quizzes.attempt_detail', compact('attempt'));
    }
}
