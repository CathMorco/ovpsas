<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $hasFilesTable = Schema::hasTable('files');
        $totalFiles = $hasFilesTable ? DB::table('files')->count() : 125;
        $activeOffices = Schema::hasTable('offices') ? DB::table('offices')->count() : 8;
        $filesThisMonth = $hasFilesTable ? DB::table('files')->whereMonth('created_at', Carbon::now()->month)->count() : 42;
        $mostActiveOffice = "ARCDO";

        $activities = [
            ['date' => Carbon::now()->format('Y-m-d'), 'office' => 'ARCDO', 'action' => 'Uploaded Executive Order'],
            ['date' => Carbon::now()->subDay()->format('Y-m-d'), 'office' => 'UCCA', 'action' => 'Generated Quarterly Report'],
            ['date' => Carbon::now()->subDays(2)->format('Y-m-d'), 'office' => 'OSFA', 'action' => 'Updated Budget Documentation'],
        ];

        return view('reports.index', compact('totalFiles', 'activeOffices', 'activities', 'filesThisMonth', 'mostActiveOffice'));
    }

    public function download()
    {
        // 1. Fetch real data from the database
        $announcements = \App\Models\Announcement::with('user')->latest()->get();
        
        // 2. Prepare counts
        $totalCount = $announcements->count();
        $activeUnits = $announcements->flatMap(function ($a) {
            return is_array($a->office) ? $a->office : [$a->office];
        })->unique()->count();

        // 3. Prepare the data array (MAKE SURE 'title' IS HERE)
        $data = [
            'title' => 'OVPSAS Portal System Utilization Report', // <--- This was missing!
            'totalCount' => $totalCount,
            'activeUnits' => $activeUnits,
            'announcements' => $announcements,
            'generatedAt' => \Carbon\Carbon::now()->format('F j, Y'),
        ];

        // 4. Load the view and download
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', $data);
        
        return $pdf->download('OVPSAS Portal-Official-Report.pdf');
    }
}