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
    Route::get('/home', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/intern/dashboard');
        }
    })->name('home');

    // Legacy dashboard redirect for backward compatibility
    Route::get('/dashboard', function () {
        return redirect('/home');
    });

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/upload-video', [AdminController::class, 'uploadVideo'])->name('upload.video');
        Route::post('/update-video-duration', [AdminController::class, 'updateVideoDuration'])->name('update.video.duration');
        Route::get('/upload-video', function () {
            return view('admin.upload');
        })->name('upload.form');
        Route::get('/interns', [AdminController::class, 'manageInterns'])->name('interns');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/diagnostics', [AdminController::class, 'diagnostics'])->name('diagnostics');
    });

    // Intern routes
    Route::middleware(['intern'])->prefix('intern')->name('intern.')->group(function () {
        Route::get('/dashboard', [InternController::class, 'dashboard'])->name('dashboard');
    });

    // Video routes (accessible by interns)
    Route::middleware(['intern'])->group(function () {
        Route::get('/video/{id}', [VideoController::class, 'watch'])->name('video.watch');
        Route::post('/save-progress', [VideoController::class, 'saveProgress'])->name('video.save.progress');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
