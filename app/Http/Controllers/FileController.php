<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileRecord;
use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display the files within a specific office folder, grouped by category.
     */
    public function showOfficeFolder($office)
    {
        $directory = "offices/{$office}";

        // Use allFiles() to grab everything inside category subfolders
        $files = Storage::disk('public')->exists($directory)
                 ? Storage::disk('public')->allFiles($directory)
                 : [];

        // This creates a collection and groups them by their folder name
        $groupedFiles = collect($files)->map(function($path) {
            $parts = explode('/', $path);

            // If path is offices/ARCDO/Memorandums/file.pdf:
            // index 0 = offices, index 1 = ARCDO, index 2 = Memorandums
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

        if ($request->hasFile('attachment')) {
            $office = $request->input('office');
            $category = $request->input('category');
            $file = $request->file('attachment');

            $fileName = $file->getClientOriginalName();

            // Organizes files into: offices/ARCDO/Memorandums/filename.pdf
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

        return back()->with('success', 'Published to ' . $request->office . ' under ' . $request->category . ' successfully!');
    }

    /**
     * Delete a file AND its corresponding Announcement record
     */
    public function destroyFile(Request $request)
    {
        $path = $request->input('file_path');

        // 1. Delete the physical file from storage
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // 2. Find the announcement record linked to this file path and delete it
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

        FileRecord::create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return back()->with('success', 'File data saved successfully!');
    }

    /**
     * Handles "Import File" (Actual file upload)
     */
    public function import(Request $request)
    {
        $request->validate([
            'uploaded_file' => 'required|file|max:1024000',
        ]);

        if ($request->hasFile('uploaded_file')) {
            $file = $request->file('uploaded_file');
            $path = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');

            FileRecord::create([
                'title' => $file->getClientOriginalName(),
                'file_path' => $path,
            ]);
        }

        return back()->with('success', 'File imported successfully!');
    }
}
