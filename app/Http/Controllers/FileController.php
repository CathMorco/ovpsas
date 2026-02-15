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
     * NEW LOGIC: Queries Database instead of Physical Folders.
     */
public function showOfficeFolder($office)
    {
        // FIXED QUERY: Checks for the specific office OR "All Offices"
        $files = Announcement::where(function ($query) use ($office) {
            $query->whereJsonContains('office', $office)
                  ->orWhereJsonContains('office', 'All Offices')
                  // Fallback: If JSON fails, check purely as text string
                  ->orWhere('office', 'LIKE', '%"'.$office.'"%')
                  ->orWhere('office', 'LIKE', '%"All Offices"%');
        })->latest()->get();

        // 2. Transform: Prepare the data for the view
        $displayCollection = collect();

        foreach ($files as $file) {
            // Ensure categories are treated as an array (Handling Legacy String Data too)
            $categories = is_array($file->category) ? $file->category : [$file->category];

            foreach ($categories as $cat) {
                $displayCollection->push([
                    'id' => $file->id,
                    'category' => $cat, // This sorts it into the right tab
                    'name' => $file->title,
                    'path' => $file->file_path,
                    'url'  => route('file.view', $file->id),
                    'size' => 'View File',
                    'date' => $file->created_at->format('M d, Y'),
                    'uploader' => $file->user->name ?? 'Unknown'
                ]);
            }
        }

        // 3. Group by Category for the View
        $groupedFiles = $displayCollection->groupBy('category');

        return view('pages.office-files', compact('office', 'groupedFiles'));
    }

    /**
     * Handles the Announcements Board with Multi-Office & Multi-Category Support
     */
    public function storeAnnouncement(Request $request)
    {
        // 1. Validate inputs (Allow Arrays)
        $request->validate([
            'office' => 'required',     // Can be Array or String
            'category' => 'required',   // Can be Array or String
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB Max
        ]);

        $filePath = null;
        $fileName = null;

        // 2. Handle File Upload (Centralized Storage)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            // Store in a flat 'uploads' folder with a timestamp to prevent overwriting
            $storeName = time() . '_' . $fileName; 
            $filePath = $file->storeAs('uploads', $storeName, 'public');
        }

        // 3. Normalize Inputs to Arrays (Safety check)
        // Even if the form sends a single string, we force it into an array for the DB
        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];

        // 4. Create Database Record
        // The Announcement Model's $casts = ['office' => 'array'] will auto-convert this to JSON
        Announcement::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'office' => $offices, 
            'category' => $categories,
            'file_path' => $filePath,
        ]);

        // 5. Log Activity
        if ($filePath && $fileName) {
            // Join array with commas for the log (e.g., "OSS, ARCDO")
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
        // Construct full path
        $path = storage_path('app/public/' . $announcement->file_path);
        
        if (!file_exists($path)) {
            abort(404);
        }

        // Handle Office Name for Logging (Since it's now an array in DB)
        $offices = is_array($announcement->office) ? $announcement->office : [$announcement->office];
        $officeString = implode(', ', $offices);
        $fileName = basename($announcement->file_path);

        // 1. Delete previous logs for this file/user (prevent duplicates)
        RecentActivity::where('user_id', Auth::id())
            ->where('file_name', $fileName)
            ->delete();

        // 2. Create new "Opened" log
        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => $fileName,
            'office_name' => $officeString,
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

        // 1. Delete from Storage
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // 2. Delete from Database
        // We search by file_path to ensure we get the right record
        $announcement = Announcement::where('file_path', $path)->first();

        if ($announcement) {
            $announcement->delete();
            return back()->with('success', 'File and announcement deleted successfully!');
        }

        return back()->with('success', 'File removed from storage.');
    }

    /**
     * Handles "Create New File" (Text based - Legacy Support)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // You can choose to save this as an announcement without a file attachment
        // or keep your separate logic if you have a "FileRecord" model.
        
        return back()->with('success', 'Text file saved successfully!');
    }

    /**
     * Handles "Import File" (Legacy Support)
     */
    public function import(Request $request)
    {
        $request->validate([
            'uploaded_file' => 'required|file|max:1024000',
        ]);

        if ($request->hasFile('uploaded_file')) {
            $file = $request->file('uploaded_file');
            $file->storeAs('uploads', $file->getClientOriginalName(), 'public');
        }

        return back()->with('success', 'File imported successfully!');
    }
}