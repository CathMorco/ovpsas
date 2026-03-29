<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * MASTER MANAGEMENT (Super Admin Only)
     * Displays the User Directory with Search and Filter capabilities.
     */
    public function index(Request $request)
    {
        // Start the query and load the office relationship to prevent N+1 database issues
        // Only load users whose status is 'active'
        $query = User::with('office')->where('status', 'active');

        // Apply Search Filter (by Name)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply Office Filter
        if ($request->filled('office')) {
            $query->where('office_id', $request->office);
        }

        // Apply Role Filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Fetch users, sorting alphabetically by name
        $users = $query->orderBy('name')->get();

        $offices = Office::all();

        // Return the view matching your filename
        return view('admin.users_management', compact('users', 'offices'));
    }

    /**
     * REGISTRATION APPROVALS (Both Admin & Super Admin)
     */
    public function approvals()
    {
        $pendingUsers = User::with('office')->where('status', 'Pending')->get();
        return view('admin.approvals', compact('pendingUsers'));
    }

    /**
     * ACTION: CHANGE ROLES
     */
    public function updateRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:Super Admin,Admin,Office Staff,Viewer',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Safety check: Prevent a Super Admin from accidentally demoting themselves
        if (auth()->id() === $user->id && $request->role !== 'Super Admin') {
            return back()->with('error', 'You cannot demote yourself.');
        }

        // Update the role
        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    /**
     * ACTION: APPROVE NEW USER
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        
        // Set status to 'active' so the login system lets them in
        $user->update(['status' => 'active']);
        
        return back()->with('success', "{$user->name} admitted to system.");
    }

    /**
     * ACTION: DECLINE/DELETE USER
     */
    public function declineUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', "Registration request removed.");
    }
}