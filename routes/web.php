<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController; // Make sure you created this!
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---

Route::get('/', function () {
    $announcements = Announcement::latest()->take(6)->get();
    return view('welcome', compact('announcements'));
});

Route::get('/about', function () {
   return view('pages.about');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

// Office Folder View (Public or Protected? Usually public for viewing)
Route::get('/offices/{office}', [FileController::class, 'showOfficeFolder'])->name('offices.show');


// --- Protected Routes (Requires Login) ---

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // 1. Profile Information (Name, Email)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Note: profile.destroy is moved to settings.destroy below

    // 2. Account Settings (Password, Delete Account)
    // You need to create SettingsController for this to work as discussed!
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');

    // 3. Announcement & Comments
    Route::post('/announcements/store', [FileController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::post('/announcements/{announcement}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 4. File Management
    Route::delete('/files/delete', [FileController::class, 'destroyFile'])->name('files.destroy');
    Route::post('/files/store', [FileController::class, 'store'])->name('files.store');
    Route::post('/files/import', [FileController::class, 'import'])->name('files.import');
});

require __DIR__.'/auth.php';