<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\RecentActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

            $attachments = [];
            if ($file->file_path) {
                $decoded = json_decode($file->file_path, true);
                if (is_array($decoded)) {
                    $attachments = $decoded;
                } else {
                    $attachments[] = ['path' => $file->file_path, 'original_name' => basename($file->file_path)];
                }
            }

            foreach ($categories as $cat) {
                $folderName = $cat;
                if ($cat === 'Others' && !empty($file->custom_category)) {
                    $folderName = $file->custom_category;
                }

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        $displayCollection->push([
                            'id'       => $file->id,
                            'category' => $folderName, 
                            'name'     => $attachment['original_name'] ?? basename($attachment['path']),
                            'path'     => $attachment['path'],
                            'url'      => route('file.view', ['announcement' => $file->id, 'path' => $attachment['path']]),
                            'size'     => 'File Attachment',
                            'date'     => $file->created_at->timezone('Asia/Manila')->format('M d, Y'),
                            'uploader' => $file->user->name ?? 'Unknown'
                        ]);
                    }
                } else {
                    $displayCollection->push([
                        'id'       => $file->id,
                        'category' => $folderName, 
                        'name'     => $file->title . " (Post Content).txt",
                        'path'     => null,
                        'url'      => route('file.view', $file->id),
                        'size'     => 'Text Content',
                        'date'     => $file->created_at->timezone('Asia/Manila')->format('M d, Y'),
                        'uploader' => $file->user->name ?? 'Unknown'
                    ]);
                }
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
            'content' => 'required_without:attachments|nullable|string',
            'attachments.*' => 'nullable|file|max:10240', // Corrected name to match frontend
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

        $uploadedPaths = [];
        $originalNames = [];

        // Handle multiple files gracefully
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $storeName = time() . '_' . Str::random(5) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $name);
                $path = $file->storeAs('uploads', $storeName, 'public');
                
                $uploadedPaths[] = [
                    'path' => $path,
                    'original_name' => $name
                ];
                $originalNames[] = $name;
            }
        }

        $encodedPaths = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;

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
                    'file_path'       => $encodedPaths,
                ]);
            }
        }

        if (!empty($originalNames)) {
            RecentActivity::create([
                'user_id'     => $user->id,
                'file_name'   => Str::limit(implode(', ', $originalNames), 200),
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
            'content' => 'required_without:attachments|nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
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
                    return back()->with('error', 'Security Alert: You can only post files to your assigned office.');
                }
            }
        }

        // ==============================================================
        // FIND SIBLINGS (5-Second Relaxed Window to bypass MySQL truncation)
        // ==============================================================
        $originalCreatedAt = Carbon::parse($announcement->getOriginal('created_at'));
        $siblings = Announcement::where('user_id', $announcement->user_id)
            ->where('title', $announcement->getOriginal('title'))
            ->whereBetween('created_at', [
                $originalCreatedAt->copy()->subSeconds(5),
                $originalCreatedAt->copy()->addSeconds(5)
            ])
            ->get();

        // Decode existing files safely
        $existingFiles = [];
        if ($announcement->file_path) {
            $decoded = json_decode($announcement->file_path, true);
            $existingFiles = is_array($decoded) ? $decoded : [['path' => $announcement->file_path, 'original_name' => basename($announcement->file_path)]];
        }

        // Handle specific file removals checked by the user
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $pathToRemove) {
                if (Storage::disk('public')->exists($pathToRemove)) {
                    Storage::disk('public')->delete($pathToRemove);
                }
                $existingFiles = array_filter($existingFiles, fn($file) => $file['path'] !== $pathToRemove);
            }
        }

        // Append newly uploaded files
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            if (!is_array($files)) { $files = [$files]; }

            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $storeName = time() . '_' . Str::random(5) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $fileName);
                $path = $file->storeAs('uploads', $storeName, 'public');
                $existingFiles[] = ['path' => $path, 'original_name' => $fileName];
            }
        }

        $encodedPaths = !empty($existingFiles) ? json_encode(array_values($existingFiles)) : null;

        // Generate updated decoupled combinations
        $combinations = [];
        foreach ($offices as $o) {
            foreach ($categories as $c) {
                $combinations[] = ['office' => $o, 'category' => $c];
            }
        }

        // Update the active master record (preserves comments)
        $firstCombo = array_shift($combinations);
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->office = [$firstCombo['office']];
        $announcement->category = [$firstCombo['category']];
        $announcement->custom_category = $request->custom_category;
        $announcement->scheduled_date = $request->scheduled_date;
        $announcement->file_path = $encodedPaths;
        $announcement->save();

        // Purge old decoupled rows, preserving the active one
        Announcement::where('id', '!=', $announcement->id)->whereIn('id', $siblings->pluck('id'))->delete();

        // Spawn new decoupled rows
        foreach ($combinations as $combo) {
            Announcement::create([
                'user_id'         => $user->id,
                'title'           => $request->title,
                'content'         => $request->content,
                'office'          => [$combo['office']],
                'category'        => [$combo['category']],
                'custom_category' => $request->custom_category,
                'scheduled_date'  => $request->scheduled_date,
                'file_path'       => $encodedPaths,
                'created_at'      => $announcement->getOriginal('created_at'),
                'updated_at'      => now(),
            ]);
        }

        RecentActivity::create([
            'user_id'     => Auth::id(),
            'file_name'   => $request->title,
            'office_name' => implode(', ', $offices),
            'action'      => 'Edited'
        ]);

        return back()->with('success', 'Announcement successfully updated globally!');
    }

    public function viewFile(Request $request, Announcement $announcement)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view documents.');
        }

        $requestedPath = $request->query('path');
        $officeNames = is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office;

        if (empty($announcement->file_path)) {
            $fileName = Str::slug($announcement->title) . ".txt";
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

        // Support array fetching correctly
        $files = json_decode($announcement->file_path, true);
        $files = is_array($files) ? $files : [['path' => $announcement->file_path, 'original_name' => basename($announcement->file_path)]];
        
        $fileToView = null;
        if ($requestedPath) {
            $fileToView = collect($files)->firstWhere('path', $requestedPath);
        } else {
            $fileToView = $files[0] ?? null; 
        }

        if (!$fileToView || !Storage::disk('public')->exists($fileToView['path'])) {
            abort(404, 'The requested file could not be found on the server.');
        }

        $this->logActivity($announcement, $fileToView['original_name'] ?? basename($fileToView['path']), $officeNames);

        return response()->file(storage_path('app/public/' . $fileToView['path']));
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
            $offices = !empty($allOfficeCodes) ? $allOfficeCodes : ['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'];
        }
        if (count($categories) > 1 && $currentCategory && in_array($currentCategory, $categories)) {
            $announcement->update(['category' => array_values(array_diff($categories, [$currentCategory]))]);
            return back()->with('success', "Removed from $currentCategory. It remains safe elsewhere.");
        } 
        if (count($offices) > 1 && $currentOffice && in_array($currentOffice, $offices)) {
            $announcement->update(['office' => array_values(array_diff($offices, [$currentOffice]))]);
            return back()->with('success', "Removed from $currentOffice. It remains safe elsewhere.");
        }

        if (!empty($announcement->file_path)) {
            $sharedCount = Announcement::where('file_path', $announcement->file_path)->where('id', '!=', $id)->count();
            
            if ($sharedCount === 0) {
                $pathsToDelete = [];
                if (Str::startsWith($announcement->file_path, '[')) {
                    $decoded = json_decode($announcement->file_path, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $f) { $pathsToDelete[] = $f['path'] ?? $f; }
                    }
                } else {
                    $pathsToDelete[] = $announcement->file_path;
                }

                foreach ($pathsToDelete as $path) {
                    if ($path && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }

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