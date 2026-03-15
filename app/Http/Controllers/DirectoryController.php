<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Start Query & Load Office Data
        $query = User::with('office')->where('status', 'approved');

        // 2. HIDE SUPER ADMIN (Custom Logic)
        // Assuming the first user (ID 1) is the Super Admin. 
        // You can also hide by email: $query->where('email', '!=', 'admin@yourschool.edu');
        $query->where('id', '!=', 1);

        // 3. Search Logic (Name, Email, or Designation)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // 4. Filter by Office
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->input('office_id'));
        }

        // 5. Paginate (12 cards per page)
        // Order by Name A-Z
        $users = $query->orderBy('name', 'asc')->paginate(12);

        // 6. Get Offices for Dropdown
        $offices = Office::orderBy('name', 'asc')->get();

        return view('directory.index', compact('users', 'offices'));
    }
}