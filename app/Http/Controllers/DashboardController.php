<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. FETCH ALL DATA
        $allData = Announcement::with(['user', 'comments.user'])->latest()->get();

        // 2. SEPARATION LOGIC
        // calendarEvents: Anything that has a valid scheduled_date
        $calendarEvents = $allData->filter(function ($item) {
            return !empty($item->scheduled_date);
        });

        // announcements: Everything else (Standard posts/announcements)
        $announcements = $allData->filter(function ($item) {
            return empty($item->scheduled_date);
        });

        // 3. CHART DATA
        // Note: Using 'flatten' because your categories/offices are stored as arrays
        $categoryData = $announcements->pluck('category')->flatten()->groupBy(fn($item) => $item)
            ->map(fn($group, $key) => (object) ['category' => $key, 'total' => $group->count()])
            ->values();

        $officeData = $announcements->pluck('office')->flatten()->groupBy(fn($item) => $item)
            ->map(fn($group, $key) => (object) ['office' => $key, 'total' => $group->count()])
            ->values();

        // 4. METRICS
        $totalFiles = $announcements->count();
        $activeOffices = $announcements->pluck('office')->flatten()->unique()->count();

        // Use created_at for monthly stats
        $filesThisMonth = $announcements->filter(function($i) {
            return optional($i->created_at)->isCurrentMonth();
        })->count();

        $mostActiveOffice = $officeData->sortByDesc('total')->first()->office ?? 'None';

        $activities = $allData->take(5)->map(function($item) {
            return [
                'date' => $item->created_at->format('M d, Y'),
                // Display first office if it's an array
                'office' => is_array($item->office) ? ($item->office[0] ?? 'N/A') : $item->office,
                'action' => 'Published: ' . Str::limit($item->title, 20)
            ];
        });

        // 5. RETURN EVERYTHING
        return view('dashboard', compact(
            'announcements',
            'calendarEvents',
            'categoryData',
            'officeData',
            'totalFiles',
            'activeOffices',
            'filesThisMonth',
            'mostActiveOffice',
            'activities'
        ));
    }
}
