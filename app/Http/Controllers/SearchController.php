<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');

        // 1. Search Users (Eager load 'office' for faster UI rendering)
        $users = User::with('office')
                     ->where('name', 'LIKE', "%{$query}%")
                     ->orWhere('email', 'LIKE', "%{$query}%")
                     ->get();

        // 2. Search Announcements/Files
        $announcements = Announcement::where('title', 'LIKE', "%{$query}%")
                                     ->orWhere('content', 'LIKE', "%{$query}%")
                                     ->orWhere('office', 'LIKE', "%{$query}%") 
                                     ->orWhere('file_path', 'LIKE', "%{$query}%") // <-- NEW: Makes filenames searchable!
                                     ->get();

        return view('search.results', compact('users', 'announcements', 'query'));
    }
}