<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display the Approvals Dashboard
     */
    public function approvals()
    {
        // Fetch all users who are currently stuck in 'pending' status
        $pendingUsers = User::where('status', 'pending')
                            ->with('office') // Assuming you want to see their requested office
                            ->orderBy('created_at', 'asc')
                            ->get();

        return view('admin.approvals', compact('pendingUsers'));
    }

    /**
     * Handle the Approval and Role Assignment
     */
    public function approveUser(Request $request, $id)
    {
        $userToApprove = User::findOrFail($id);
        $currentUser = Auth::user();

        // Ensure a role was selected from the dropdown in the UI
        $request->validate([
            'role' => ['required', 'string', 'in:Admin,Office Staff,Viewer']
        ]);

        // CRITICAL SECURITY CHECK:
        // If someone is trying to approve a user as an 'Admin', verify the person clicking approve is a Super Admin.
        if ($request->role === 'Admin' && !$currentUser->isSuperAdmin()) {
            return back()->with('error', 'Action Denied: Only Super Admins can promote users to the Admin role.');
        }

        // If it passes, update the user to active and assign the chosen role
        $userToApprove->update([
            'status' => 'active',
            'role' => $request->role
        ]);

        return back()->with('success', $userToApprove->name . ' has been approved as an ' . $request->role . '.');
    }

    /**
     * Decline and Remove the User
     */
    public function declineUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Or update status to 'declined' if you want to keep the record

        return back()->with('success', 'Registration request declined and removed.');
    }
}