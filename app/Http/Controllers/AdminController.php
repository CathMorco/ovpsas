<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * MASTER MANAGEMENT (Super Admin Only)
     * Admins are redirected away from here by middleware.
     */
    public function index()
    {
        // Super Admins see all approved users.
        // We sort by 'requesting_admin' so people asking for promotion appear at the top!
        $users = User::with('office')
            ->where('status', 'Approved')
            ->orderBy('requesting_admin', 'desc') 
            ->orderBy('name', 'asc')
            ->get();

        $offices = Office::all();
        return view('admin.usermanagement', compact('users', 'offices'));
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
        
        // Safety check
        if (auth()->id() === $user->id && $request->role !== 'Super Admin') {
            return back()->with('error', 'You cannot demote yourself.');
        }

        // If promoted to Admin, clear the request flag
        $user->update([
            'role' => $request->role,
            'requesting_admin' => ($request->role === 'Admin' || $request->role === 'Super Admin') ? false : $user->requesting_admin
        ]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'Approved']);
        return back()->with('success', "{$user->name} admitted to system.");
    }

    public function declineUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', "Registration request removed.");
    }
}