<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth; // Better to import the Facade
use Illuminate\Support\Facades\Storage;

class AnnouncementsController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        // Ensure office.* and category.* are validated to ensure the array contents are strings
        $validated = $request->validate([
            'office'     => 'required|array|min:1',
            'office.*'   => 'string',
            'category'   => 'required|array|min:1',
            'category.*' => 'string',
            'title'      => 'nullable|string|max:255',
            'content'    => 'required|string',
            'file'       => 'nullable|file|max:5000',
        ]);

        // 2. Instance Creation
        $announcement = new Announcement();

        // Use Auth::id() for clarity
        $announcement->user_id = Auth::id();

        // Assigning validated data
        $announcement->office = $validated['office'];
        $announcement->category = $validated['category'];
        $announcement->title = $validated['title'];
        $announcement->content = $validated['content'];

        // 3. File Handling
        if ($request->hasFile('file')) {
            // This stores in storage/app/public/announcements
            $path = $request->file('file')->store('announcements', 'public');
            $announcement->file_path = $path;
        }

        $announcement->save();

        return back()->with('success', 'Announcement posted!');
    }
}
