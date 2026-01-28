<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController; // Added this import
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    $announcements = Announcement::latest()->take(6)->get();
    return view('welcome', compact('announcements'));
});

Route::get('/about', function () {
   return view('pages.about');
});

Route::get('/offices/{office}', [FileController::class, 'showOfficeFolder'])->name('offices.show');

// Protected Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Announcement Routes
    Route::post('/announcements/store', [FileController::class, 'storeAnnouncement'])->name('announcements.store');

    // Comment Route - NEW
    Route::post('/announcements/{announcement}/comments', [CommentController::class, 'store'])->name('comments.store');

    // File Management
    Route::delete('/files/delete', [FileController::class, 'destroyFile'])->name('files.destroy');
    Route::post('/files/store', [FileController::class, 'store'])->name('files.store');
    Route::post('/files/import', [FileController::class, 'import'])->name('files.import');
});

require __DIR__.'/auth.php';
