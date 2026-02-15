<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search'); // 'search' matches the input name in your form

        // 1. Search Users (Name or Email)
        $users = User::where('name', 'LIKE', "%{$query}%")
                     ->orWhere('email', 'LIKE', "%{$query}%")
                     ->get();

        // 2. Search Announcements/Files (Title, Content, or Office)
        $announcements = Announcement::where('title', 'LIKE', "%{$query}%")
                                     ->orWhere('content', 'LIKE', "%{$query}%")
                                     ->orWhere('office', 'LIKE', "%{$query}%") // Finds files from a specific office
                                     ->get();

        // Return the view with both sets of data
        return view('search.results', compact('users', 'announcements', 'query'));
    }
}