<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\RecentActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Display the files for a specific office.
     * SECURED: Redirects guests to login. Staff can only view their assigned office.
     */
    public function showOfficeFolder($office)
    {
        // 1. GUEST PROTECTION: Block access to folders if not logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the Document Repositories.');
        }

        $user = Auth::user();

        // 2. STAFF PROTECTION: Staff can only view their own assigned office folder
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            if ($user->office && $user->office->code !== $office) {
                abort(403, 'Unauthorized Access: You can only view your own office repository.');
            }
        }

        $files = Announcement::where(function ($query) use ($office) {
            $query->whereJsonContains('office', $office)
                  ->orWhereJsonContains('office', 'All Offices')
                  ->orWhere('office', 'LIKE', '%"'.$office.'"%')
                  ->orWhere('office', 'LIKE', '%"All Offices"%');
        })->latest()->get();

        $displayCollection = collect();

        foreach ($files as $file) {
            $categories = is_array($file->category) ? $file->category : [$file->category];

            foreach ($categories as $cat) {
                $folderName = $cat;
                if ($cat === 'Others' && !empty($file->custom_category)) {
                    $folderName = $file->custom_category;
                }

                $displayName = $file->file_path ? $file->title : $file->title . " (Post Content).txt";

                $displayCollection->push([
                    'id'       => $file->id,
                    'category' => $folderName, 
                    'name'     => $displayName,
                    'path'     => $file->file_path,
                    'url'      => route('file.view', $file->id),
                    'size'     => $file->file_path ? 'File Attachment' : 'Text Content',
                    'date'     => $file->created_at->timezone('Asia/Manila')->format('M d, Y'),
                    'uploader' => $file->user->name ?? 'Unknown'
                ]);
            }
        }

        $groupedFiles = $displayCollection->groupBy('category');

        return view('pages.office-files', compact('office', 'groupedFiles'));
    }

    /**
     * Store Announcement Logic
     */
    public function storeAnnouncement(Request $request)
    {
        $user = Auth::user();
        
        // Safeguard: Ensure user is logged in
        if (!$user) return redirect()->route('login');

        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        $request->validate([
            'office' => 'required',
            'category' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required_without:attachment|nullable|string',
            'attachment' => 'required_without:content|nullable|file|max:10240',
            'custom_category' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
        ]);

        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];

        // 3. SECURITY: Check Category & Office Restrictions for Staff
        if (!$isAdmin) {
            // Block Staff from uploading Memos or EOs
            $restrictedCats = ['Memorandums', 'Executive Orders'];
            foreach ($categories as $cat) {
                if (in_array($cat, $restrictedCats)) {
                    return back()->with('error', 'Security Alert: Only OSAS Admins can upload Memorandums and Executive Orders.');
                }
            }

            // Block Staff from posting to other offices
            $userOfficeCode = $user->office ? $user->office->code : null;
            foreach ($offices as $targetOffice) {
                if ($targetOffice !== $userOfficeCode) {
                    return back()->with('error', 'Security Alert: You can only post files to your assigned office (' . $userOfficeCode . ').');
                }
            }
        }

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $storeName = time() . '_' . $fileName;
            $filePath = $file->storeAs('uploads', $storeName, 'public');
        }

        Announcement::create([
            'user_id'         => $user->id,
            'title'           => $request->title,
            'content'         => $request->content,
            'office'          => $offices,
            'category'        => $categories,
            'custom_category' => $request->custom_category,
            'scheduled_date'  => $request->scheduled_date,
            'file_path'       => $filePath,
        ]);

        if ($filePath && $fileName) {
            RecentActivity::create([
                'user_id'     => $user->id,
                'file_name'   => $fileName,
                'office_name' => implode(', ', $offices),
                'action'      => 'Uploaded'
            ]);
        }

        return back()->with('success', 'Published successfully!');
    }

    /**
     * VIEW FILE: Generates a virtual .txt file if no physical file exists.
     * SECURED: Redirects guests to login.
     */
    public function viewFile(Announcement $announcement)
    {
        // GUEST PROTECTION: Block viewing files if not logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view documents.');
        }

        if (empty($announcement->file_path)) {
            $fileName = Str::slug($announcement->title) . ".txt";
            $officeNames = is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office;
            
            $txtContent  = "TITLE: " . $announcement->title . "\r\n";
            $txtContent .= "DATE: " . $announcement->created_at->timezone('Asia/Manila')->format('F d, Y h:i A') . "\r\n";
            $txtContent .= "OFFICE(S): " . $officeNames . "\r\n";
            $txtContent .= "--------------------------------------------------\r\n\r\n";
            $txtContent .= $announcement->content;

            $this->logActivity($announcement, $fileName, $officeNames);

            return response($txtContent, 200, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        $path = storage_path('app/public/' . $announcement->file_path);

        if (!file_exists($path)) {
            abort(404, 'The requested file could not be found on the server.');
        }

        $offices = is_array($announcement->office) ? $announcement->office : [$announcement->office];
        $this->logActivity($announcement, basename($announcement->file_path), implode(', ', $offices));

        return response()->file($path);
    }

    private function logActivity(Announcement $announcement, $fileName, $officeString)
    {
        RecentActivity::where('user_id', Auth::id())
            ->where('file_name', $fileName)
            ->delete();

        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => $fileName,
            'office_name' => $officeString,
            'action'      => 'Opened'
        ]);
    }

    public function destroyFile(Request $request)
    {
        $id = $request->input('id');
        $path = $request->input('file_path');

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        if ($id) {
            Announcement::where('id', $id)->delete();
            return back()->with('success', 'File and announcement deleted successfully!');
        }

        return back()->with('success', 'File removed from storage.');
    }
}