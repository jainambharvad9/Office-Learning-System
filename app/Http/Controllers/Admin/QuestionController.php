<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->get();

        return view('admin.questions.index', compact('quiz', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Quiz $quiz)
    {

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'points' => 'required|integer|min:1',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required|integer'
        ]);

        // Create question
        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'order' => $quiz->questions()->count() + 1
        ]);

        // Create options
        if ($validated['question_type'] === 'multiple_choice') {
            foreach ($validated['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => ($index + 1) == $validated['correct_option'],
                    'order' => $index + 1
                ]);
            }
        } else {
            // True/False options
            $question->options()->create(['option_text' => 'True', 'is_correct' => $validated['correct_option'] == 1, 'order' => 1]);
            $question->options()->create(['option_text' => 'False', 'is_correct' => $validated['correct_option'] == 2, 'order' => 2]);
        }

        return redirect()->route('admin.questions.index', $quiz)
            ->with('success', 'Question added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        $question->load('options', 'quiz');
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $question->load('options', 'quiz');
        return view('admin.questions.edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'points' => 'required|integer|min:1',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required|integer'
        ]);

        // Update question
        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points']
        ]);

        // Delete existing options
        $question->options()->delete();

        // Create new options
        if ($validated['question_type'] === 'multiple_choice') {
            foreach ($validated['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => ($index + 1) == $validated['correct_option'],
                    'order' => $index + 1
                ]);
            }
        } else {
            // True/False options
            $question->options()->create(['option_text' => 'True', 'is_correct' => $validated['correct_option'] == 1, 'order' => 1]);
            $question->options()->create(['option_text' => 'False', 'is_correct' => $validated['correct_option'] == 2, 'order' => 2]);
        }

        return redirect()->route('admin.questions.index', $question->quiz)
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $quiz = $question->quiz;
        $question->delete();

        return redirect()->route('admin.questions.index', $quiz)
            ->with('success', 'Question deleted successfully!');
    }
}
