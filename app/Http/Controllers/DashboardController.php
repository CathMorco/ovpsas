<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. FETCH LIVE DATA (From Code 2) ---
        // Fetch announcements with authors and comments
        $announcements = Announcement::with(['user', 'comments.user'])->latest()->get();

        // Data for Pie Chart
        $categoryData = Announcement::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // Data for Bar Chart
        $officeData = Announcement::select('office', DB::raw('count(*) as total'))
            ->groupBy('office')
            ->get();


        // --- 2. CALCULATE REPORT METRICS (Real data replacing Code 1) ---
        
        // Total Files (Count of all announcements)
        $totalFiles = $announcements->count();

        // Active Offices (Count unique offices that have uploaded)
        $activeOffices = $announcements->unique('office')->count();

        // Files This Month (Filter the collection we already have)
        $filesThisMonth = $announcements->filter(function ($item) {
            return $item->created_at->month === Carbon::now()->month;
        })->count();

        // Most Active Office (Sort the office data we fetched for the chart)
        $mostActiveOffice = $officeData->sortByDesc('total')->first()->office ?? 'None';

        // Recent Activities (Map the latest 5 announcements to the format the View expects)
        $activities = $announcements->take(5)->map(function($item) {
            return [
                'date' => $item->created_at->format('M d, Y'),
                'office' => $item->office,
                'action' => 'Published: ' . Str::limit($item->title, 20)
            ];
        });

        // --- 3. RETURN EVERYTHING ---
        return view('dashboard', compact(
            'announcements', 
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