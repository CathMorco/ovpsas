<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Office;
use App\Models\RecentActivity; // <-- ADDED
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the System Administrative Report and Feed.
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();
        $userOfficeCode = $user->office ? $user->office->code : null;

        // ==========================================
        // 1. ISOLATION & RAW DATA (For Analytics)
        // ==========================================
        $query = Announcement::with(['user', 'comments.user'])->latest();

        if (!$isAdmin) {
            $query->where(function($q) use ($userOfficeCode) {
                $q->whereJsonContains('office', $userOfficeCode)
                  ->orWhereJsonContains('office', 'All Offices')
                  ->orWhere('office', 'LIKE', '%"'.$userOfficeCode.'"%')
                  ->orWhere('office', 'LIKE', '%"All Offices"%');
            });
        }

        $rawAnnouncements = $query->get();

        // ==========================================
        // 2. DEDUPLICATION LOGIC (For the Feed Only)
        // ==========================================
        $groupedAnnouncements = $rawAnnouncements->groupBy(function($item) {
            return $item->title . '|' . $item->content . '|' . $item->created_at->format('Y-m-d H:i');
        })->map(function($group) {
            $base = $group->first();
            
            $allOffices = $group->pluck('office')->flatten()->unique()->filter()->values()->toArray();
            $allCategories = $group->pluck('category')->flatten()->unique()->filter()->values()->toArray();
            
            $base->office = $allOffices;
            $base->category = $allCategories;
            
            return $base;
        })->values()->sortByDesc('created_at');

        // ==========================================
        // 3. DYNAMIC CATEGORY EXTRACTION
        // ==========================================
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

        // ==========================================
        // 4. UI DISPLAY VARIABLES & ACTIVITY FEED
        // ==========================================
        
        $upcomingEvents = $groupedAnnouncements->filter(function ($item) {
            return !empty($item->scheduled_date) && 
                   Carbon::parse($item->scheduled_date)->isAfter(now()->startOfDay());
        })->sortBy('scheduled_date');

        $feedItems = $groupedAnnouncements; 
        $repositoryFiles = $groupedAnnouncements->filter(fn($item) => !empty($item->file_path));

        // GET RECENT ACTIVITY FOR SIDEBAR
        $recentActivities = RecentActivity::with('user')->latest()->take(15)->get(); // <-- ADDED

        // ==========================================
        // 5. CHART & ANALYTICS (Uses RAW Data)
        // ==========================================
        $categoryData = $rawAnnouncements->pluck('category')->flatten()->filter()
            ->groupBy(fn($cat) => $cat)
            ->map(fn($group, $key) => (object) ['category' => $key, 'total' => $group->count()])
            ->values();

        $allOfficeCodes = Office::pluck('code')->toArray();
        $defaultOffices = !empty($allOfficeCodes) ? $allOfficeCodes : ['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'];
        
        $expandedOffices = collect();

        foreach ($rawAnnouncements as $item) {
            $offices = is_array($item->office) ? $item->office : [$item->office];
            
            if (in_array('All Offices', $offices)) {
                foreach ($defaultOffices as $do) {
                    $expandedOffices->push($do);
                }
            } else {
                foreach ($offices as $o) {
                    $expandedOffices->push($o);
                }
            }
        }

        $officeData = $expandedOffices->filter()
            ->groupBy(fn($off) => $off)
            ->map(fn($group, $key) => (object) ['office' => $key, 'total' => $group->count()])
            ->values();

        $filteredOfficeData = $officeData->reject(fn($o) => 
            in_array(strtolower($o->office), ['general', 'all offices'])
        )->sortBy('office')->values();

        // ==========================================
        // 6. PRIMARY METRICS (Uses RAW Data)
        // ==========================================
        $totalActualFiles = $rawAnnouncements->count(); 
        $monitoredOfficesCount = $filteredOfficeData->count();

        $filesThisMonthCount = $rawAnnouncements->filter(function($item) {
            return $item->created_at && $item->created_at->isCurrentMonth();
        })->count();

        $mostActiveOffice = $filteredOfficeData->sortByDesc('total')->first()->office ?? 'N/A';

        // ==========================================
        // 7. RETURN VIEW
        // ==========================================
        return view('dashboard', [
            'upcomingEvents'        => $upcomingEvents,
            'feedItems'             => $feedItems,
            'repositoryFiles'       => $repositoryFiles,
            'recentActivities'      => $recentActivities, // <-- ADDED
            'totalActualFiles'      => $totalActualFiles,
            'monitoredOfficesCount' => $monitoredOfficesCount,
            'filesThisMonthCount'   => $filesThisMonthCount,
            'mostActiveOffice'      => $mostActiveOffice,
            'categoryData'          => $categoryData,
            'filteredOfficeData'    => $filteredOfficeData,
            'allAvailableCategories'=> $allAvailableCategories
        ]);
    }
}