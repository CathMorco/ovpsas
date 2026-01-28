<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Comment; // Added this just to be safe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch announcements with the person who posted it AND all comments with their authors
        $announcements = Announcement::with(['user', 'comments.user'])->latest()->get();

        // Data for the Pie Chart
        $categoryData = Announcement::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // Data for the Bar Chart
        $officeData = Announcement::select('office', DB::raw('count(*) as total'))
            ->groupBy('office')
            ->get();

        return view('dashboard', compact('announcements', 'categoryData', 'officeData'));
    }
}
