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
        $query = User::with('office');

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

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fetch users, sorting those requesting admin access to the very top, then alphabetically
        $users = $query->orderByDesc('requesting_admin')
                       ->orderBy('name')
                       ->get();

        $offices = Office::all();

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
     * ACTION: STAFF REQUESTS PROMOTION
     */
    public function requestPromotion()
    {
        auth()->user()->update(['requesting_admin' => true]);
        return back()->with('success', 'Your request for Admin status has been submitted to the Super Admin.');
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

        // Update the role. If promoted to Admin/Super Admin, clear the 'requesting_admin' flag automatically.
        $user->update([
            'role' => $request->role,
            'requesting_admin' => ($request->role === 'Admin' || $request->role === 'Super Admin') ? false : $user->requesting_admin
        ]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    /**
     * ACTION: APPROVE NEW USER
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'Approved']);
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