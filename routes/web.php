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
    // 1. Fetch enough data so the Category columns (Memos/EOs) are populated
    $rawAnnouncements = Announcement::with(['user', 'comments.user'])->latest()->get();

    // 2. Deduplicate simultaneous decoupled uploads for the visual Feed
    $announcements = $rawAnnouncements->groupBy(function($item) {
        return $item->title . '|' . $item->content . '|' . $item->created_at->format('Y-m-d H:i');
    })->map(function($group) {
        $base = $group->first();
        $base->office = $group->pluck('office')->flatten()->unique()->filter()->values()->toArray();
        $base->category = $group->pluck('category')->flatten()->unique()->filter()->values()->toArray();
        
        // Bundle paths into temporary array for UI display
        $files = $group->map(function($item) {
            return $item->file_path ? ['path' => $item->file_path, 'original_name' => basename($item->file_path)] : null;
        })->filter()->unique('path')->values()->toArray();
        
        $base->temp_files = $files; 
        return $base;
    })->values()->sortByDesc('created_at');

    // 3. Dynamic Category Extraction
    $allCategoriesInDb = Announcement::pluck('category')->flatten()->unique()->toArray();
    $defaultCategories = ['Memorandums', 'Executive Orders', 'Reports', 'Minutes of Meeting', 'Activity Proposals', 'Letters', 'Financials', 'Forms', 'Policies', 'MOAs', 'Masterlists', 'Event Material'];

    $allAvailableCategories = collect(array_merge($defaultCategories, $allCategoriesInDb))
        ->reject(fn($c) => strtolower(trim($c)) === 'others')
        ->unique()->sort()->values()->toArray();
    $allAvailableCategories[] = 'Others';

    return view('welcome', compact('announcements', 'allAvailableCategories'));
});

Route::get('/about', function () { return view('pages.about'); });
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/offices/{office}', [FileController::class, 'showOfficeFolder'])->name('offices.show');

// --- Protected Routes ---
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::delete('/announcements/{announcement}', [FileController::class, 'destroyAnnouncement'])->name('announcements.destroy');

    Route::post('/announcements/{announcement}/comments', [CommentController::class, 'store'])->name('comments.store');

    // File Management & Viewing
    Route::get('/file/view/{announcement}', [FileController::class, 'viewFile'])->name('file.view'); 
    Route::get('/file/download/{announcement}', [FileController::class, 'downloadFile'])->name('file.download'); // <--- ADDED THIS LINE
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