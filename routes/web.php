<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DirectoryController; 
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---

Route::get('/', function () {
    $announcements = Announcement::latest()->take(6)->get();

    $allCategoriesInDb = Announcement::pluck('category')->flatten()->unique()->toArray();
    
    $defaultCategories = [
        'Memorandums', 'Executive Orders', 'Reports', 'Minutes of Meeting', 
        'Activity Proposals', 'Letters', 'Financials', 'Forms', 
        'Policies', 'MOAs', 'Masterlists', 'Event Material'
    ];

    $allAvailableCategories = collect(array_merge($defaultCategories, $allCategoriesInDb))
        ->reject(fn($c) => strtolower(trim($c)) === 'others')
        ->unique()->sort()->values()->toArray();
    
    $allAvailableCategories[] = 'Others';

    return view('welcome', compact('announcements', 'allAvailableCategories'));
});

Route::get('/about', function () {
   return view('pages.about');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');

Route::get('/offices/{office}', [FileController::class, 'showOfficeFolder'])->name('offices.show');


// --- Protected Routes (Requires Login) ---

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');

    // Announcement Management
    Route::post('/announcements/store', [FileController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [FileController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [FileController::class, 'destroyAnnouncement'])->name('announcements.destroy'); // <-- ADDED FOR POSTER DELETE

    Route::post('/announcements/{announcement}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/file/view/{announcement}', [FileController::class, 'viewFile'])->name('file.view'); 
    Route::delete('/files/delete', [FileController::class, 'destroyFile'])->name('files.destroy');
    Route::post('/files/store', [FileController::class, 'store'])->name('files.store');
    Route::post('/files/import', [FileController::class, 'import'])->name('files.import');

    Route::post('/admin/request-promotion', [AdminController::class, 'requestPromotion'])->name('admin.request_promotion');

    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::get('/userslist', [AdminController::class, 'index'])->name('users.list');
        Route::get('/approvals', [AdminController::class, 'approvals'])->name('admin.approvals');
        Route::post('/userslist/{id}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
        Route::put('/userslist/{id}/update-designation', [AdminController::class, 'updateDesignation'])->name('admin.users.updateDesignation');
        Route::delete('/userslist/{id}/decline', [AdminController::class, 'declineUser'])->name('admin.users.decline');
        
        Route::middleware([SuperAdminMiddleware::class])->group(function () {
            Route::put('/users/role/update', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
        });
    });
});

require __DIR__.'/auth.php';