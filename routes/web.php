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
    });

    // Intern routes
    Route::middleware(['intern'])->prefix('intern')->name('intern.')->group(function () {
        Route::get('/dashboard', [InternController::class, 'dashboard'])->name('dashboard');
    });

    // Video routes (accessible by interns)
    Route::middleware(['intern'])->group(function () {
        Route::get('/video/{id}', [VideoController::class, 'watch'])->name('video.watch');
        Route::post('/save-progress', [VideoController::class, 'saveProgress'])->name('video.save.progress');
        Route::post('/update-video-duration', [VideoController::class, 'updateDuration'])->name('video.update.duration');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/interns/register', [InternController::class, 'register'])->name('admin.interns.register');
});
