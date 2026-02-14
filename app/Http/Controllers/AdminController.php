<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Office; 

class AdminController extends Controller
{
    // 1. Show User List (Active Users)
    public function index()
    {
        $users = User::all();
        $offices = Office::all();
        return view('userslist', compact('users', 'offices'));
    }

    // 2. Show Approvals Page (Pending Users) + Search Role Logic
    public function approvals(Request $request)
    {
        // A. Fetch Pending Users for the list
        $pendingUsers = User::where('status', '!=', 'approved')
                            ->orWhereNull('status')
                            ->orderBy('created_at', 'desc')
                            ->get();

        // B. Search Logic for Role Management
        $searchedUser = null;
        if ($request->filled('search_email')) {
            $searchedUser = User::where('email', $request->search_email)->first();
        }

        return view('approvals', compact('pendingUsers', 'searchedUser'));
    }

    // 3. Update Designation (For User List)
    public function updateDesignation(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'designation' => 'required|string|max:255',
            'office_id'   => 'required|exists:offices,id',
        ]);

        $user->update([
            'designation' => $request->designation,
            'office_id'   => $request->office_id,
        ]);

        return redirect()->back()->with('success', 'User designation updated successfully.');
    }

    // 4. Approve User
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        return redirect()->back()->with('success', 'User has been approved successfully.');
    }

    // 5. Decline/Delete User
    public function declineUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); 

        return redirect()->back()->with('success', 'User request has been declined and removed.');
    }

    // 6. Update User Role (New Feature)
    public function updateRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,staff,viewer', 
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Safety: Prevent changing your own role
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.approvals')->with('error', 'Security Alert: You cannot change your own role.');
        }

        $user->role = $request->role;
        $user->save();

        // Redirect back, keeping the search active so you see the result
        return redirect()->route('admin.approvals', ['search_email' => $user->email])
                         ->with('success', "Role updated to '{$request->role}' successfully.");
    }
}