<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\RecentActivity; // <--- NEEDED for Dashboard Sidebar
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;   // <--- NEEDED for User ID tracking

class FileController extends Controller
{
    /**
     * Display the files within a specific office folder, grouped by category.
     */
    public function showOfficeFolder($office)
    {
        $directory = "offices/{$office}";

        $files = Storage::disk('public')->exists($directory)
                 ? Storage::disk('public')->allFiles($directory)
                 : [];

        $groupedFiles = collect($files)->map(function($path) {
            $parts = explode('/', $path);
            $category = (count($parts) > 2) ? $parts[2] : 'General';

            return [
                'category' => $category,
                'name' => basename($path),
                'path' => $path,
                'url'  => asset('storage/' . $path),
                'size' => round(Storage::disk('public')->size($path) / 1024, 2) . ' KB'
            ];
        })->groupBy('category');

        return view('pages.office-files', compact('office', 'groupedFiles'));
    }

    /**
     * Handles the Announcements Board with Office Folders and Categories
     */
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'office' => 'required|string',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('attachment')) {
            $office = $request->input('office');
            $category = $request->input('category');
            $file = $request->file('attachment');

            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs("offices/{$office}/{$category}", $fileName, 'public');
        }

        Announcement::create([
            'user_id' => auth()->id(),
            'office' => $request->office,
            'category' => $request->category,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
        ]);

        // --- LOG ACTIVITY: Uploaded ---
        if ($filePath && $fileName) {
            RecentActivity::create([
                'user_id'     => Auth::id(),
                'file_name'   => $fileName,
                'office_name' => $request->office, 
                'action'      => 'Uploaded'
            ]);
        }

        return back()->with('success', 'Published to ' . $request->office . ' under ' . $request->category . ' successfully!');
    }

    /**
     * VIEW FILE: Logs activity and shows the file.
     * REQUIRED for Dashboard Side Panel to work.
     */
    public function viewFile(Announcement $announcement)
    {
        $path = storage_path('app/public/' . $announcement->file_path);
        
        if (!file_exists($path)) {
            abort(404);
        }

        // 1. Delete previous logs for this file/user to prevent duplicates (bumping it to top)
        RecentActivity::where('user_id', Auth::id())
            ->where('file_name', basename($announcement->file_path))
            ->where('office_name', $announcement->office)
            ->delete();

        // 2. Create new "Opened" log
        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => basename($announcement->file_path),
            'office_name' => $announcement->office,
            'action'      => 'Opened'
        ]);

        return response()->file($path);
    }

    /**
     * Delete a file AND its corresponding Announcement record
     */
    public function destroyFile(Request $request)
    {
        $path = $request->input('file_path');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $announcement = Announcement::where('file_path', $path)->first();

        if ($announcement) {
            $announcement->delete();
            return back()->with('success', 'File and announcement deleted successfully!');
        }

        return back()->with('success', 'File removed from storage.');
    }

    /**
     * Handles "Create New File" (Text based)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // FileRecord logic removed as per your request in Code 2
        
        return back()->with('success', 'File data saved successfully!');
    }

    /**
     * Handles "Import File"
     */
    public function import(Request $request)
    {
        $request->validate([
            'uploaded_file' => 'required|file|max:1024000',
        ]);

        if ($request->hasFile('uploaded_file')) {
            $file = $request->file('uploaded_file');
            $path = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');
            
            // FileRecord logic removed as per your request in Code 2
        }

        return back()->with('success', 'File imported successfully!');
    }
}