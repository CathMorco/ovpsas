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
        if (!Auth::check()) return redirect()->route('login')->with('error', 'Please log in.');

        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            if ($user->office && $user->office->code !== $office) abort(403);
        }

        $announcements = Announcement::where(function ($query) use ($office) {
            $query->whereJsonContains('office', $office)
                  ->orWhereJsonContains('office', 'All Offices')
                  ->orWhere('office', 'LIKE', '%"'.$office.'"%')
                  ->orWhere('office', 'LIKE', '%"All Offices"%');
        })->latest()->get();

        $displayCollection = collect();
        foreach ($announcements as $post) {
            $cat = is_array($post->category) ? ($post->category[0] ?? 'General') : $post->category;
            if ($cat === 'Others' && !empty($post->custom_category)) $cat = $post->custom_category;

            $files = json_decode($post->file_path, true);

            if (is_array($files) && count($files) > 0) {
                foreach ($files as $file) {
                    $originalName = $file['original_name'] ?? basename($file['path']);
                    $displayCollection->push([
                        'id' => $post->id, 
                        'category' => $cat, 
                        'name' => $post->title . '  —  📄 ' . $originalName,
                        'path' => $file['path'], 
                        'url' => route('file.view', ['announcement' => $post->id, 'path' => $file['path']]),
                        'download_url' => route('file.download', ['announcement' => $post->id, 'path' => $file['path']]),
                        'size' => 'File Attachment',
                        'date' => $post->created_at->timezone('Asia/Manila')->format('M d, Y'),
                        'uploader' => $post->user->name ?? 'Unknown',
                        'link' => $post->link 
                    ]);
                }
            } else {
                $displayCollection->push([
                    'id' => $post->id, 
                    'category' => $cat, 
                    'name' => $post->title . " — 📝 (Post Content)",
                    'path' => null, 
                    'url' => route('file.view', $post->id),
                    'download_url' => route('file.download', $post->id),
                    'size' => 'Text Content',
                    'date' => $post->created_at->timezone('Asia/Manila')->format('M d, Y'),
                    'uploader' => $post->user->name ?? 'Unknown',
                    'link' => $post->link 
                ]);
            }
        }

        $groupedFiles = $displayCollection->groupBy('category');
        return view('pages.office-files', compact('office', 'groupedFiles'));
    }

    public function storeAnnouncement(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'office' => 'required', 'category' => 'required', 'title' => 'required|string|max:255',
            'content' => 'required_without_all:attachment,link|nullable|string', 
            'attachment.*' => 'file|max:10240',
            'link' => 'nullable|url|max:2048'
        ]);

        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];

        if (in_array('Others', $categories) && !empty($request->custom_category)) {
            $categories = array_diff($categories, ['Others']);
            $categories[] = trim($request->custom_category);
        }
        $categories = array_values(array_unique($categories));

        if (in_array('All Offices', $offices)) {
            $offices = \App\Models\Office::pluck('code')->toArray();
        }

        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            $restrictedCats = ['Memorandums', 'Executive Orders'];
            foreach ($categories as $cat) {
                if (in_array($cat, $restrictedCats)) {
                    return back()->with('error', 'Security Alert: Only OVPSAS Portal Admins can upload Memorandums and Executive Orders.');
                }
            }

            $userOfficeCode = $user->office ? $user->office->code : null;
            foreach ($offices as $targetOffice) {
                if ($targetOffice !== $userOfficeCode) {
                    return back()->with('error', 'Security Alert: You can only post files to your assigned office.');
                }
            }
        }

        $filePaths = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $originalName = $file->getClientOriginalName();
                $storeName = time() . '_' . Str::random(5) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $originalName);
                $path = $file->storeAs('uploads', $storeName, 'public');
                $filePaths[] = ['path' => $path, 'original_name' => $originalName];
            }
        }

        $encodedPaths = !empty($filePaths) ? json_encode($filePaths) : null;

        foreach ($offices as $o) {
            foreach ($categories as $c) {
                Announcement::create([
                    'user_id' => $user->id, 'title' => $request->title, 'content' => $request->content,
                    'office' => [$o], 'category' => [$c], 'custom_category' => $request->custom_category,
                    'scheduled_date' => $request->scheduled_date, 'file_path' => $encodedPaths,
                    'link' => $request->link
                ]);
            }
        }
        return back()->with('success', 'Published successfully!');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $user = Auth::user();
        if ($user->id !== $announcement->user_id) abort(403);

        $request->validate([
            'office' => 'required', 'category' => 'required', 'title' => 'required|string|max:255',
            'content' => 'nullable|string', 'attachment.*' => 'nullable|file|max:10240',
            'link' => 'nullable|url|max:2048'
        ]);

        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];
        if (in_array('All Offices', $offices)) $offices = ['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'];

        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            $restrictedCats = ['Memorandums', 'Executive Orders'];
            foreach ($categories as $cat) {
                if (in_array($cat, $restrictedCats)) {
                    return back()->with('error', 'Security Alert: Only OVPSAS Portal Admins can upload Memorandums and Executive Orders.');
                }
            }
        }

        // PERFECT SIBLING DELETION (Wide 60s window catches everything to guarantee no duplicates)
        $originalCreatedAt = Carbon::parse($announcement->getOriginal('created_at'));
        $siblings = Announcement::where('user_id', $user->id)->where('title', $announcement->getOriginal('title'))
            ->whereBetween('created_at', [$originalCreatedAt->copy()->subSeconds(60), $originalCreatedAt->copy()->addSeconds(60)])->get();

        $existingFiles = [];
        if ($announcement->file_path) {
            $decoded = json_decode($announcement->file_path, true);
            $existingFiles = is_array($decoded) ? $decoded : [['path' => $announcement->file_path, 'original_name' => basename($announcement->file_path)]];
        }

        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $pathToRemove) {
                if (Storage::disk('public')->exists($pathToRemove)) Storage::disk('public')->delete($pathToRemove);
                $existingFiles = array_filter($existingFiles, fn($f) => $f['path'] !== $pathToRemove);
            }
        }

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $originalName = $file->getClientOriginalName();
                $storeName = time() . '_' . Str::random(5) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $originalName);
                $path = $file->storeAs('uploads', $storeName, 'public');
                $existingFiles[] = ['path' => $path, 'original_name' => $originalName];
            }
        }

        $encodedPaths = !empty($existingFiles) ? json_encode(array_values($existingFiles)) : null;

        $combinations = [];
        foreach ($offices as $o) { foreach ($categories as $c) { $combinations[] = ['o' => $o, 'c' => $c]; } }

        $first = array_shift($combinations);
        
        // 1. Update the root post to keep comments alive
        $announcement->update([
            'title' => $request->title, 'content' => $request->content, 
            'office' => [$first['o']], 'category' => [$first['c']], 
            'file_path' => $encodedPaths, 'scheduled_date' => $request->scheduled_date,
            'link' => $request->link, 'updated_at' => now()
        ]);

        // 2. Eradicate all outdated ghost copies to prevent duplicates
        $siblingIdsToDelete = $siblings->pluck('id')->reject(fn($id) => $id == $announcement->id);
        Announcement::whereIn('id', $siblingIdsToDelete)->delete();

        // 3. Create fresh copies
        foreach ($combinations as $combo) {
            Announcement::create([
                'user_id' => $user->id, 'title' => $request->title, 'content' => $request->content, 
                'office' => [$combo['o']], 'category' => [$combo['c']], 'file_path' => $encodedPaths, 
                'scheduled_date' => $request->scheduled_date, 'created_at' => $announcement->getOriginal('created_at'),
                'link' => $request->link, 'updated_at' => now()
            ]);
        }

        return back()->with('success', 'Announcement updated successfully!');
    }

    public function viewFile(Request $request, Announcement $announcement)
    {
        return $this->handleFileResponse($request, $announcement, false);
    }

    public function downloadFile(Request $request, Announcement $announcement)
    {
        return $this->handleFileResponse($request, $announcement, true);
    }

    private function handleFileResponse(Request $request, Announcement $announcement, $isDownload)
    {
        if (!Auth::check()) return redirect()->route('login');
        
        $requestedPath = $request->query('path');
        $officeNames = is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office;

        if (empty($announcement->file_path)) {
            $fileName = Str::slug($announcement->title) . ".txt";
            $txtContent = "TITLE: " . $announcement->title . "\r\nDATE: " . $announcement->created_at->format('F d, Y') . "\r\nOFFICES: " . $officeNames . "\r\n----------------\r\n\r\n" . $announcement->content;
            
            if ($announcement->link) {
                $txtContent .= "\r\n\r\nACCESS LINK: " . $announcement->link;
            }

            $this->logActivity($announcement, $fileName, $officeNames, $isDownload ? 'Downloaded' : 'Opened');
            
            return response($txtContent, 200, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => ($isDownload ? 'attachment' : 'inline') . '; filename="' . $fileName . '"'
            ]);
        }

        $files = json_decode($announcement->file_path, true);
        if (!is_array($files)) $files = [['path' => $announcement->file_path, 'original_name' => basename($announcement->file_path)]];
        
        $fileToProcess = $requestedPath ? collect($files)->firstWhere('path', $requestedPath) : ($files[0] ?? null);

        if (!$fileToProcess || !Storage::disk('public')->exists($fileToProcess['path'])) abort(404);

        $this->logActivity($announcement, $fileToProcess['original_name'] ?? basename($fileToProcess['path']), $officeNames, $isDownload ? 'Downloaded' : 'Opened');

        if ($isDownload) {
            return response()->download(storage_path('app/public/' . $fileToProcess['path']), $fileToProcess['original_name'] ?? null);
        }
        return response()->file(storage_path('app/public/' . $fileToProcess['path']));
    }

    private function logActivity(Announcement $announcement, $fileName, $officeString, $action = 'Opened')
    {
        RecentActivity::where('user_id', Auth::id())->where('file_name', $fileName)->delete();
        RecentActivity::create(['user_id' => Auth::id(), 'file_name' => $fileName, 'office_name' => $officeString, 'action' => $action]);
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $user = Auth::user();
        if ($user->id !== $announcement->user_id) abort(403);

        $originalCreatedAt = Carbon::parse($announcement->getOriginal('created_at'));
        $siblings = Announcement::where('user_id', $user->id)->where('title', $announcement->getOriginal('title'))
            ->whereBetween('created_at', [$originalCreatedAt->copy()->subSeconds(60), $originalCreatedAt->copy()->addSeconds(60)])->get();

        if ($announcement->file_path) {
            $files = json_decode($announcement->file_path, true);
            $pathsToDelete = is_array($files) ? $files : [['path' => $announcement->file_path]];
            foreach ($pathsToDelete as $file) {
                if (Storage::disk('public')->exists($file['path'])) Storage::disk('public')->delete($file['path']);
            }
        }

        RecentActivity::create(['user_id' => $user->id, 'file_name' => $announcement->title, 'office_name' => 'Global', 'action' => 'Deleted Post']);
        Announcement::whereIn('id', $siblings->pluck('id'))->delete();
        return back()->with('success', 'Post fully removed.');
    }

    public function destroyFile(Request $request)
    {
        $id = $request->input('id');
        $pathToRemove = $request->input('file_path');
        $announcement = Announcement::findOrFail($id);
        
        // TRUE ISOLATED FILE DELETION (Safely extracts arrays to prevent accidental multi-deletes)
        if ($pathToRemove && $announcement->file_path) {
            $files = json_decode($announcement->file_path, true);
            if (is_array($files)) {
                $remainingFiles = array_filter($files, fn($f) => ($f['path'] ?? null) !== $pathToRemove);
                
                $sharedCount = Announcement::where('file_path', 'LIKE', '%' . $pathToRemove . '%')->where('id', '!=', $id)->count();
                if ($sharedCount === 0 && Storage::disk('public')->exists($pathToRemove)) {
                    Storage::disk('public')->delete($pathToRemove);
                }

                if (count($remainingFiles) > 0) {
                    $announcement->update(['file_path' => json_encode(array_values($remainingFiles))]);
                    RecentActivity::create(['user_id' => Auth::id(), 'file_name' => basename($pathToRemove), 'office_name' => 'Folder', 'action' => 'Deleted File']);
                    return back()->with('success', 'File removed safely.');
                } else {
                    if (empty($announcement->content) && empty($announcement->link)) {
                        $announcement->delete();
                    } else {
                        $announcement->update(['file_path' => null]);
                    }
                    RecentActivity::create(['user_id' => Auth::id(), 'file_name' => basename($pathToRemove), 'office_name' => 'Folder', 'action' => 'Deleted File']);
                    return back()->with('success', 'File removed successfully.');
                }
            }
        }
        
        // Fallback for full item deletion
        RecentActivity::create(['user_id' => Auth::id(), 'file_name' => $announcement->title, 'office_name' => 'Folder', 'action' => 'Deleted Item']);
        $announcement->delete();
        return back()->with('success', 'Item removed successfully.');
    }
}