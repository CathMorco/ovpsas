<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * MASTER DIRECTORY / MANAGEMENT
     * Shows all approved faculty and staff.
     */
    public function index()
    {
        $users = User::with('office')->where('status', 'Approved')->get();
        $offices = Office::all();
        return view('admin.users_management', compact('users', 'offices'));
    }

    /**
     * APPROVALS QUEUE
     * Shows users who registered but aren't admitted yet.
     */
    public function approvals()
    {
        // Grabs everyone with "Pending" status
        $pendingUsers = User::with('office')->where('status', 'Pending')->get();
        return view('admin.approvals', compact('pendingUsers'));
    }

    /**
     * ACTION: ADMIT FACULTY
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'Approved']);

        return back()->with('success', "{$user->name} has been admitted to the system.");
    }

    /**
     * ACTION: REJECT REGISTRATION
     */
    public function declineUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Removes the request from the DB

        return back()->with('success', "Registration request removed.");
    }

    /**
     * SUPER ADMIN: ROLE MANAGEMENT
     */
    public function updateRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:Super Admin,Admin,Office Staff,Viewer',
        ]);

        $user = User::findOrFail($request->user_id);

        if (auth()->id() === $user->id && $request->role !== 'Super Admin') {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $user->update(['role' => $request->role]);
        return back()->with('success', "Role for {$user->name} updated to {$request->role}.");
    }

    public function updateDesignation(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'designation' => $request->designation,
            'office_id' => $request->office_id
        ]);

        return back()->with('success', 'User details updated.');
    }
}