<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\RecentActivity;
use Illuminate\Support\Facades\Auth;

class AnnouncementsController extends Controller
{
    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'office' => 'required|array',
            'category' => 'required|array',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|max:10240', // Matches the name="file" in your Blade view
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            // Store in public/uploads to keep it clean
            $filePath = $file->storeAs('uploads', time() . '_' . $fileName, 'public');
        }

        Announcement::create([
            'user_id' => Auth::id(),
            'office' => $request->office,
            'category' => $request->category,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
        ]);

        // Optional: Log activity for the Dashboard Sidebar
        RecentActivity::create([
            'user_id' => Auth::id(),
            'file_name' => $fileName ?? 'Announcement: ' . $request->title,
            'office_name' => implode(', ', $request->office),
            'action' => 'Uploaded'
        ]);

        return back()->with('success', 'Announcement published successfully!');
    }
}