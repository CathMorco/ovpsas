<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Change this to whatever model you want to search (e.g., App\Models\Student)

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get the search term from the URL
        $search = $request->input('query');

        // 2. Search the database (This example searches the 'name' or 'email' columns)
        // If the search is empty, return nothing
        $results = collect();
        
        if($search){
            $results = User::where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%")
                           ->get();
        }

        // 3. Return the results view with the data
        return view('search.results', compact('results', 'search'));
    }
}