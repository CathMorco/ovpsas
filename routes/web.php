<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReportController; // Required for Reports
use App\Http\Controllers\AdminController;  // Required for Admin
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware; // Required for Admin Protection

// --- Public Routes ---

Route::get('/', function () {
    $announcements = Announcement::latest()->take(6)->get();
    return view('welcome', compact('announcements'));
});

Route::get('/about', function () {
   return view('pages.about');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

// Office Folder View
Route::get('/offices/{office}', [FileController::class, 'showOfficeFolder'])->name('offices.show');


// --- Protected Routes (Requires Login) ---

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // 1. Profile Information (View Only)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    
    // 2. Account Settings (Update Profile, Password, Avatar, Delete Account)
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update'); // Using PATCH
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');

    // 3. Reports & Analytics (From Code 2)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');

    // 4. Announcement & Comments
    Route::post('/announcements/store', [FileController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::post('/announcements/{announcement}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 5. File Management & Viewing (From Code 2 - Critical for Logging)
    Route::get('/file/view/{announcement}', [FileController::class, 'viewFile'])->name('file.view'); 
    
    Route::delete('/files/delete', [FileController::class, 'destroyFile'])->name('files.destroy');
    Route::post('/files/store', [FileController::class, 'store'])->name('files.store');
    Route::post('/files/import', [FileController::class, 'import'])->name('files.import');

    // 6. Admin Routes (From Code 1 - Secured by Middleware)
    Route::middleware([AdminMiddleware::class])->group(function () {
        
        // Pages
        Route::get('/userslist', [AdminController::class, 'index'])->name('users.list');
        Route::get('/approvals', [AdminController::class, 'approvals'])->name('admin.approvals');

        // Actions
        Route::post('/userslist/{id}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
        Route::put('/userslist/{id}/update-designation', [AdminController::class, 'updateDesignation'])->name('admin.users.updateDesignation');
        Route::delete('/userslist/{id}/decline', [AdminController::class, 'declineUser'])->name('admin.users.decline');
        
        // Update User Role
        Route::put('/users/role/update', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    });

});

require __DIR__.'/auth.php';