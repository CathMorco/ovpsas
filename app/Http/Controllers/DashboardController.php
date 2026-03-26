<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Fetch ALL announcements with relationships
        $all = Announcement::with(['user', 'comments.user'])->latest()->get();

        // 2. Partitioning for display
        $upcomingEvents = $all->filter(fn($i) => 
            !empty($i->scheduled_date) && 
            Carbon::parse($i->scheduled_date)->isAfter(now()->startOfDay())
        )->sortBy('scheduled_date');

        // Feed shows EVERYTHING
        $feedItems = $all; 

        // Repository shows only items with files
        $repositoryFiles = $all->filter(fn($i) => !empty($i->file_path));

        // 3. Chart Data Calculations
        $categoryData = $all->pluck('category')->flatten()->filter()->groupBy(fn($c) => $c)
            ->map(fn($g, $key) => (object)['category' => $key, 'total' => $g->count()])->values();

        $officeData = $all->pluck('office')->flatten()->filter()->groupBy(fn($o) => $o)
            ->map(fn($g, $key) => (object)['office' => $key, 'total' => $g->count()])->values();

        // Filtered data for the Bar Chart (Excludes 'General' labels for cleaner visual)
        $filteredOfficeData = $officeData->reject(fn($o) => 
            in_array(strtolower($o->office), ['general', 'all offices'])
        );

        // 4. Final Stats
        $totalActualFiles = $all->count();
        $filesThisMonthCount = $all->filter(fn($i) => $i->created_at->isCurrentMonth())->count();
        $mostActiveOffice = $filteredOfficeData->sortByDesc('total')->first()->office ?? 'N/A';

        return view('dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'feedItems' => $feedItems,
            'repositoryFiles' => $repositoryFiles,
            'totalActualFiles' => $totalActualFiles,
            'monitoredOfficesCount' => $filteredOfficeData->count(),
            'filesThisMonthCount' => $filesThisMonthCount,
            'mostActiveOffice' => $mostActiveOffice,
            'categoryData' => $categoryData,
            'filteredOfficeData' => $filteredOfficeData // <--- Fixed variable name
        ]);
    }
}