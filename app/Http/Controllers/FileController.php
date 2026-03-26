<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\RecentActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Display the files for a specific office.
     * UPDATED: Now creates dynamic folders based on custom input.
     */
    public function showOfficeFolder($office)
    {
        // 1. Query: Checks for the specific office OR "All Offices"
        $files = Announcement::where(function ($query) use ($office) {
            $query->whereJsonContains('office', $office)
                  ->orWhereJsonContains('office', 'All Offices')
                  ->orWhere('office', 'LIKE', '%"'.$office.'"%')
                  ->orWhere('office', 'LIKE', '%"All Offices"%');
        })->latest()->get();

        // 2. Transform: Prepare the data for the view
        $displayCollection = collect();

        foreach ($files as $file) {
            // Ensure categories are treated as an array
            $categories = is_array($file->category) ? $file->category : [$file->category];

            foreach ($categories as $cat) {
                // LOGIC: If 'Others' is selected, use the custom input as the folder name
                $folderName = $cat;
                if ($cat === 'Others' && !empty($file->custom_category)) {
                    $folderName = $file->custom_category;
                }

                $displayCollection->push([
                    'id' => $file->id,
                    'category' => $folderName, // This determines the folder grouping
                    'name' => $file->title,
                    'path' => $file->file_path,
                    'url'  => route('file.view', $file->id),
                    'size' => 'View File',
                    'date' => $file->created_at->format('M d, Y'),
                    'uploader' => $file->user->name ?? 'Unknown'
                ]);
            }
        }

        // 3. Group by the dynamic folder name
        $groupedFiles = $displayCollection->groupBy('category');

        return view('pages.office-files', compact('office', 'groupedFiles'));
    }

    /**
     * Handles the Announcements Board with Multi-Office & Custom Category Support
     */
/**
     * Handles the Announcements Board with Multi-Office & Date Support
     */
    public function storeAnnouncement(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'office' => 'required',
            'category' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required_without:attachment|nullable|string',
            'attachment' => 'required_without:content|nullable|file|max:10240',
            'custom_category' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date', // <--- Added validation
        ], [
            'content.required_without' => 'Please provide a description or upload a file.',
            'attachment.required_without' => 'Please upload a file or type a description.',
        ]);

        $filePath = null;
        $fileName = null;

        // 2. Handle File Upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $storeName = time() . '_' . $fileName;
            $filePath = $file->storeAs('uploads', $storeName, 'public');
        }

        // 3. Normalize Inputs
        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];

        // 4. Create Database Record
        Announcement::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'office' => $offices,
            'category' => $categories,
            'custom_category' => $request->custom_category,
            'scheduled_date' => $request->scheduled_date, // <--- Save to DB
            'file_path' => $filePath,
        ]);

        // 5. Log Activity
        if ($filePath && $fileName) {
            $officeString = implode(', ', $offices);
            RecentActivity::create([
                'user_id'     => Auth::id(),
                'file_name'   => $fileName,
                'office_name' => $officeString,
                'action'      => 'Uploaded'
            ]);
        }

        return back()->with('success', 'Published successfully!');
    }

    /**
     * VIEW FILE: Logs activity and shows the file.
     */
    public function viewFile(Announcement $announcement)
    {
        $path = storage_path('app/public/' . $announcement->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        $offices = is_array($announcement->office) ? $announcement->office : [$announcement->office];
        $officeString = implode(', ', $offices);
        $fileName = basename($announcement->file_path);

        RecentActivity::where('user_id', Auth::id())
            ->where('file_name', $fileName)
            ->delete();

        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => $fileName,
            'office_name' => $officeString,
            'action'      => 'Opened'
        ]);

        return response()->file($path);
    }

    /**
     * Delete a file AND its record
     */
    public function destroyFile(Request $request)
    {
        $path = $request->input('file_path');

        if ($path && Storage::disk('public')->exists($path)) {
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
     * Legacy/Support Methods
     */
    public function store(Request $request) { /* ... same as before ... */ }
    public function import(Request $request) { /* ... same as before ... */ }
}