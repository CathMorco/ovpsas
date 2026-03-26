<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the System Administrative Report and Feed.
     */
    public function index()
    {
        // 1. Fetch ALL data with essential relationships
        $all = Announcement::with(['user', 'comments.user'])->latest()->get();

        // 2. Partitioning Logic for UI Display
        
        // SIDEBAR CALENDAR: Items that have a scheduled date in the future (or today)
        $upcomingEvents = $all->filter(function ($item) {
            return !empty($item->scheduled_date) && 
                   Carbon::parse($item->scheduled_date)->isAfter(now()->startOfDay());
        })->sortBy('scheduled_date');

        // ANNOUNCEMENT FEED: Show everything (No rejection logic to ensure feed isn't empty)
        $feedItems = $all; 

        // REPOSITORY: Only items that have a physical file attachment
        $repositoryFiles = $all->filter(fn($item) => !empty($item->file_path));

        // 3. Analytic & Chart Data Calculations
        
        // Category Distribution (Flattened because categories are stored as JSON/Arrays)
        $categoryData = $all->pluck('category')->flatten()->filter()
            ->groupBy(fn($cat) => $cat)
            ->map(fn($group, $key) => (object) ['category' => $key, 'total' => $group->count()])
            ->values();

        // Office Distribution
        $officeData = $all->pluck('office')->flatten()->filter()
            ->groupBy(fn($off) => $off)
            ->map(fn($group, $key) => (object) ['office' => $key, 'total' => $group->count()])
            ->values();

        // FILTERED OFFICE DATA: Excludes generic labels to keep the Bar Chart clean
        $filteredOfficeData = $officeData->reject(fn($o) => 
            in_array(strtolower($o->office), ['general', 'all offices'])
        );

        // 4. Primary Metrics
        
        // Total Volume of Posts/Files
        $totalActualFiles = $all->count(); 
        
        // Number of specific monitored offices active in the system
        $monitoredOfficesCount = $filteredOfficeData->count();

        // Growth metrics for the current month
        $filesThisMonthCount = $all->filter(function($item) {
            return $item->created_at && $item->created_at->isCurrentMonth();
        })->count();

        // Identify the top performer
        $mostActiveOffice = $filteredOfficeData->sortByDesc('total')->first()->office ?? 'N/A';

        // 5. Return view with all required variables
        return view('dashboard', [
            'upcomingEvents'        => $upcomingEvents,
            'feedItems'             => $feedItems,
            'repositoryFiles'       => $repositoryFiles,
            'totalActualFiles'      => $totalActualFiles,
            'monitoredOfficesCount' => $monitoredOfficesCount,
            'filesThisMonthCount'   => $filesThisMonthCount,
            'mostActiveOffice'      => $mostActiveOffice,
            'categoryData'          => $categoryData,
            'filteredOfficeData'    => $filteredOfficeData // Crucial: Fixes the Blade error
        ]);
    }
}