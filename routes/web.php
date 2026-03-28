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

    // --- DYNAMIC CATEGORY EXTRACTION FOR HOME PAGE ---
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
    // -------------------------------------------------

    return view('welcome', compact('announcements', 'allAvailableCategories'));
});

Route::get('/about', function () {
   return view('pages.about');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');

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
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');

    // 3. Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');

    // 4. Announcement & Comments
    Route::post('/announcements/store', [FileController::class, 'storeAnnouncement'])->name('announcements.store');
    
    // --- Route for updating announcements ---
    Route::put('/announcements/{announcement}', [FileController::class, 'updateAnnouncement'])->name('announcements.update');
    // ----------------------------------------

    Route::post('/announcements/{announcement}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 5. File Management & Viewing
    Route::get('/file/view/{announcement}', [FileController::class, 'viewFile'])->name('file.view'); 
    Route::delete('/files/delete', [FileController::class, 'destroyFile'])->name('files.destroy');
    Route::post('/files/store', [FileController::class, 'store'])->name('files.store');
    Route::post('/files/import', [FileController::class, 'import'])->name('files.import');

    // ========================================================================
    // Promotion Request Route (Accessible to regular Staff)
    // ========================================================================
    Route::post('/admin/request-promotion', [AdminController::class, 'requestPromotion'])->name('admin.request_promotion');

    // 6. Admin Routes (Secured by AdminMiddleware)
    Route::middleware([AdminMiddleware::class])->group(function () {
        
        // Pages
        Route::get('/userslist', [AdminController::class, 'index'])->name('users.list');
        Route::get('/approvals', [AdminController::class, 'approvals'])->name('admin.approvals');

        // Actions
        Route::post('/userslist/{id}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
        Route::put('/userslist/{id}/update-designation', [AdminController::class, 'updateDesignation'])->name('admin.users.updateDesignation');
        Route::delete('/userslist/{id}/decline', [AdminController::class, 'declineUser'])->name('admin.users.decline');
        
        // 7. Super Admin Exclusive Routes (Role Management)
        Route::middleware([SuperAdminMiddleware::class])->group(function () {
            Route::put('/users/role/update', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
            // Add other "God Mode" routes here later (e.g., Audit Logs)
        });
    });

});

require __DIR__.'/auth.php';