<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // // Legacy dashboard redirect for backward compatibility
    Route::get('/dashboard', function () {
        return redirect('/home');
    });

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/upload-video', [AdminController::class, 'uploadVideo'])->name('upload.video');
        Route::post('/update-video-duration', [AdminController::class, 'updateVideoDuration'])->name('update.video.duration');
        Route::post('/process-video-duration/{video}', [AdminController::class, 'processVideoDuration'])->name('process.video.duration');
        Route::get('/upload-video', [AdminController::class, 'showUploadForm'])->name('upload.form');
        Route::get('/videos', [AdminController::class, 'manageVideos'])->name('videos');
        Route::delete('/videos/{id}', [AdminController::class, 'deleteVideo'])->name('videos.delete');
        Route::get('/interns', [AdminController::class, 'manageInterns'])->name('interns');
        Route::post('/interns/register', [AdminController::class, 'registerIntern'])->name('interns.register');
        Route::delete('/interns/{id}', [AdminController::class, 'deleteIntern'])->name('interns.delete');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::resource('categories', AdminController::class, [
            'parameters' => ['categories' => 'category'],
            'names' => [
                'index' => 'categories',
                'create' => 'categories.create',
                'store' => 'categories.store',
                'edit' => 'categories.edit',
                'update' => 'categories.update',
                'destroy' => 'categories.destroy'
            ],
            'except' => ['show']
        ]);

        // Quiz management routes
        Route::resource('quizzes', \App\Http\Controllers\Admin\QuizController::class);
        Route::get('quizzes/{quiz}/results', [\App\Http\Controllers\Admin\QuizController::class, 'results'])->name('quizzes.results');
        Route::get('attempts/{attempt}/detail', [\App\Http\Controllers\Admin\QuizController::class, 'attemptDetail'])->name('quizzes.attempt-detail');
        Route::get('quizzes/{quiz}/questions', [\App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('questions.index');
        Route::get('quizzes/{quiz}/questions/create', [\App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('questions.create');
        Route::post('quizzes/{quiz}/questions', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('questions.store');
        Route::get('questions/{question}/edit', [\App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');
    });

    // Intern routes
    Route::middleware(['intern'])->prefix('intern')->name('intern.')->group(function () {
        Route::get('/dashboard', [InternController::class, 'dashboard'])->name('dashboard');
        Route::get('/videos', [InternController::class, 'allVideos'])->name('videos.all');
        Route::get('/search-videos', [InternController::class, 'searchVideos'])->name('search.videos');

        // Quiz routes
        Route::get('/quizzes', [\App\Http\Controllers\Intern\QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Intern\QuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/start', [\App\Http\Controllers\Intern\QuizController::class, 'start'])->name('quizzes.start');
        Route::get('/attempts/{attempt}', [\App\Http\Controllers\Intern\QuizController::class, 'take'])->name('quizzes.take');
        Route::post('/attempts/{attempt}/answer', [\App\Http\Controllers\Intern\QuizController::class, 'answer'])->name('quizzes.answer');
        Route::get('/attempts/{attempt}/result', [\App\Http\Controllers\Intern\QuizController::class, 'result'])->name('quizzes.result');
    });

    // Video routes (accessible by interns)
    Route::middleware(['intern'])->group(function () {
        Route::get('/video/{id}', [VideoController::class, 'watch'])->name('video.watch');
        Route::post('/save-progress', [VideoController::class, 'saveProgress'])->name('video.save.progress');
        Route::post('/update-video-duration', [VideoController::class, 'updateDuration'])->name('video.update.duration');
        Route::post('/video/mark-complete', [VideoController::class, 'markVideoComplete'])->name('video.mark.complete');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/interns/register', [InternController::class, 'register'])->name('admin.interns.register');
});