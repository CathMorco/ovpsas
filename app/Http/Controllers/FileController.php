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
    public function showOfficeFolder($office)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the Document Repositories.');
        }

        $user = Auth::user();

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

    public function storeAnnouncement(Request $request)
    {
        $user = Auth::user();
        
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

        if (in_array('Others', $categories) && !empty($request->custom_category)) {
            $categories = array_diff($categories, ['Others']);
            $categories[] = trim($request->custom_category);
        }
        $categories = array_values(array_unique($categories));

        if (in_array('All Offices', $offices)) {
            $allOfficeCodes = \App\Models\Office::pluck('code')->toArray();
            $offices = !empty($allOfficeCodes) ? $allOfficeCodes : ['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'];
        }

        if (!$isAdmin) {
            $restrictedCats = ['Memorandums', 'Executive Orders'];
            foreach ($categories as $cat) {
                if (in_array($cat, $restrictedCats)) {
                    return back()->with('error', 'Security Alert: Only OSAS Admins can upload Memorandums and Executive Orders.');
                }
            }

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

        foreach ($offices as $singleOffice) {
            foreach ($categories as $singleCategory) {
                Announcement::create([
                    'user_id'         => $user->id,
                    'title'           => $request->title,
                    'content'         => $request->content,
                    'office'          => [$singleOffice],
                    'category'        => [$singleCategory],
                    'custom_category' => $request->custom_category,
                    'scheduled_date'  => $request->scheduled_date,
                    'file_path'       => $filePath,
                ]);
            }
        }

        if ($filePath && $fileName) {
            RecentActivity::create([
                'user_id'     => $user->id,
                'file_name'   => $fileName,
                'office_name' => implode(', ', $offices),
                'action'      => 'Uploaded'
            ]);
        }

        return back()->with('success', 'Published successfully! Files are now safely isolated.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $user = Auth::user();
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        if ($user->id !== $announcement->user_id && !$isAdmin) {
            abort(403, 'Unauthorized action. You can only edit your own announcements.');
        }

        $request->validate([
            'office' => 'required',
            'category' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required_without:attachment|nullable|string',
            'attachment' => 'nullable|file|max:10240',
            'custom_category' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
        ]);

        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];

        if (in_array('Others', $categories) && !empty($request->custom_category)) {
            $categories = array_diff($categories, ['Others']);
            $categories[] = trim($request->custom_category);
        }
        $categories = array_values(array_unique($categories));

        if (!$isAdmin) {
            $restrictedCats = ['Memorandums', 'Executive Orders'];
            foreach ($categories as $cat) {
                if (in_array($cat, $restrictedCats)) {
                    return back()->with('error', 'Security Alert: Only OSAS Admins can upload Memorandums and Executive Orders.');
                }
            }

            $userOfficeCode = $user->office ? $user->office->code : null;
            foreach ($offices as $targetOffice) {
                if ($targetOffice !== $userOfficeCode) {
                    return back()->with('error', 'Security Alert: You can only post files to your assigned office (' . $userOfficeCode . ').');
                }
            }
        }

        if ($request->hasFile('attachment')) {
            if ($announcement->file_path && Storage::disk('public')->exists($announcement->file_path)) {
                Storage::disk('public')->delete($announcement->file_path);
            }
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $storeName = time() . '_' . $fileName;
            $announcement->file_path = $file->storeAs('uploads', $storeName, 'public');
        }

        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->office = $offices;
        $announcement->category = $categories;
        $announcement->custom_category = $request->custom_category;
        $announcement->scheduled_date = $request->scheduled_date;

        $announcement->save();

        // LOG THE EDIT IN THE ACTIVITY FEED
        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => $announcement->title,
            'office_name' => implode(', ', $offices),
            'action'      => 'Edited'
        ]);

        return back()->with('success', 'Announcement successfully updated!');
    }

    public function viewFile(Announcement $announcement)
    {
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
        $currentOffice = trim($request->input('office_context')); 
        $currentCategory = trim($request->input('category_context'));

        if (!$id) {
            return back()->with('error', 'Invalid file selection.');
        }

        $announcement = Announcement::findOrFail($id);

        $offices = is_array($announcement->office) ? $announcement->office : json_decode($announcement->office, true) ?? [];
        $categories = is_array($announcement->category) ? $announcement->category : json_decode($announcement->category, true) ?? [];

        if (in_array('All Offices', $offices)) {
            $allOfficeCodes = \App\Models\Office::pluck('code')->toArray();
            $offices = !empty($allOfficeCodes) ? $allOfficeCodes : ['ARCDO', 'OSFA', 'OSS', 'SDP', 'SPS', 'UCCA', 'UDRMC'];
        }
        if (count($categories) > 1 && $currentCategory && in_array($currentCategory, $categories)) {
            $announcement->update(['category' => array_values(array_diff($categories, [$currentCategory]))]);
            return back()->with('success', "Removed from $currentCategory. It remains safe elsewhere.");
        } 
        if (count($offices) > 1 && $currentOffice && in_array($currentOffice, $offices)) {
            $announcement->update(['office' => array_values(array_diff($offices, [$currentOffice]))]);
            return back()->with('success', "Removed from $currentOffice. It remains safe elsewhere.");
        }

        if ($announcement->file_path) {
            $sharedCount = Announcement::where('file_path', $announcement->file_path)->where('id', '!=', $id)->count();
            if ($sharedCount === 0 && Storage::disk('public')->exists($announcement->file_path)) {
                Storage::disk('public')->delete($announcement->file_path);
            }
        }

        // LOG THE DELETION IN THE ACTIVITY FEED BEFORE DELETING IT
        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => $announcement->title,
            'office_name' => is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office,
            'action'      => 'Deleted'
        ]);

        $announcement->delete();

        return back()->with('success', 'File deleted successfully from this specific folder.');
    }
}