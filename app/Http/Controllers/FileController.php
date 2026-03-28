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

        $files = Announcement::where(function ($query) use ($office) {
            $query->whereJsonContains('office', $office)
                  ->orWhereJsonContains('office', 'All Offices')
                  ->orWhere('office', 'LIKE', '%"'.$office.'"%')
                  ->orWhere('office', 'LIKE', '%"All Offices"%');
        })->latest()->get();

        $displayCollection = collect();
        foreach ($files as $file) {
            $cat = is_array($file->category) ? ($file->category[0] ?? 'General') : $file->category;
            if ($cat === 'Others' && !empty($file->custom_category)) $cat = $file->custom_category;

            $displayCollection->push([
                'id' => $file->id, 'category' => $cat, 
                'name' => $file->file_path ? basename($file->file_path) : $file->title . " (Post Content).txt",
                'path' => $file->file_path, 'url' => route('file.view', $file->id),
                'size' => $file->file_path ? 'File Attachment' : 'Text Content',
                'date' => $file->created_at->timezone('Asia/Manila')->format('M d, Y'),
                'uploader' => $file->user->name ?? 'Unknown'
            ]);
        }
        $groupedFiles = $displayCollection->groupBy('category');
        return view('pages.office-files', compact('office', 'groupedFiles'));
    }

    public function storeAnnouncement(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'office' => 'required', 'category' => 'required', 'title' => 'required|string|max:255',
            'content' => 'required_without:attachments|nullable|string', 'attachments.*' => 'file|max:10240',
        ]);

        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];

        if (in_array('Others', $categories) && !empty($request->custom_category)) {
            $categories = array_diff($categories, ['Others']);
            $categories[] = trim($request->custom_category);
        }
        $categories = array_values(array_unique($categories));

        if (in_array('All Offices', $offices)) $offices = ['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'];

        $filePaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storeName = time() . '_' . Str::random(5) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
                $filePaths[] = $file->storeAs('uploads', $storeName, 'public');
            }
        }

        $pathsToIterate = !empty($filePaths) ? $filePaths : [null];

        // FULL DECOUPLING: Creates separate row for EVERY office, category, AND file.
        foreach ($offices as $o) {
            foreach ($categories as $c) {
                foreach ($pathsToIterate as $p) {
                    Announcement::create([
                        'user_id' => $user->id, 'title' => $request->title, 'content' => $request->content,
                        'office' => [$o], 'category' => [$c], 'custom_category' => $request->custom_category,
                        'scheduled_date' => $request->scheduled_date, 'file_path' => $p,
                    ]);
                }
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
            'content' => 'nullable|string', 'attachments.*' => 'nullable|file|max:10240',
        ]);

        $offices = is_array($request->office) ? $request->office : [$request->office];
        $categories = is_array($request->category) ? $request->category : [$request->category];
        if (in_array('All Offices', $offices)) $offices = ['ARCDO', 'OCPS', 'OSFA', 'OSS', 'OUR', 'SDPO', 'UCCA'];

        // SYNC SIBLINGS LOGIC
        $originalCreatedAt = Carbon::parse($announcement->getOriginal('created_at'));
        $siblings = Announcement::where('user_id', $user->id)->where('title', $announcement->getOriginal('title'))
            ->whereBetween('created_at', [$originalCreatedAt->copy()->subSeconds(5), $originalCreatedAt->copy()->addSeconds(5)])->get();

        $existingPaths = $siblings->pluck('file_path')->filter()->unique()->toArray();

        // Handle deletions and new additions
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $pathToRemove) {
                if (Storage::disk('public')->exists($pathToRemove)) Storage::disk('public')->delete($pathToRemove);
                $existingPaths = array_filter($existingPaths, fn($p) => $p !== $pathToRemove);
            }
        }
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storeName = time() . '_' . Str::random(5) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
                $existingPaths[] = $file->storeAs('uploads', $storeName, 'public');
            }
        }

        $pathsToIterate = !empty($existingPaths) ? $existingPaths : [null];
        $combinations = [];
        foreach ($offices as $o) { foreach ($categories as $c) { foreach ($pathsToIterate as $p) { $combinations[] = ['o' => $o, 'c' => $c, 'p' => $p]; } } }

        $first = array_shift($combinations);
        $announcement->update(['title' => $request->title, 'content' => $request->content, 'office' => [$first['o']], 'category' => [$first['c']], 'file_path' => $first['p'], 'scheduled_date' => $request->scheduled_date]);

        Announcement::where('user_id', $user->id)->where('id', '!=', $announcement->id)->whereIn('id', $siblings->pluck('id'))->delete();

        foreach ($combinations as $combo) {
            Announcement::create(['user_id' => $user->id, 'title' => $request->title, 'content' => $request->content, 'office' => [$combo['o']], 'category' => [$combo['c']], 'file_path' => $combo['p'], 'scheduled_date' => $request->scheduled_date, 'created_at' => $announcement->getOriginal('created_at')]);
        }

        return back()->with('success', 'Announcement updated globally!');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $user = Auth::user();
        if ($user->id !== $announcement->user_id) abort(403);

        $originalCreatedAt = Carbon::parse($announcement->getOriginal('created_at'));
        $siblings = Announcement::where('user_id', $user->id)->where('title', $announcement->getOriginal('title'))
            ->whereBetween('created_at', [$originalCreatedAt->copy()->subSeconds(5), $originalCreatedAt->copy()->addSeconds(5)])->get();

        foreach ($siblings->pluck('file_path')->filter()->unique() as $path) {
            if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
        }

        RecentActivity::create(['user_id' => $user->id, 'file_name' => $announcement->title, 'office_name' => 'All Folders', 'action' => 'Deleted Post']);
        Announcement::whereIn('id', $siblings->pluck('id'))->delete();
        return back()->with('success', 'Post fully removed from all folders.');
    }

    public function viewFile(Request $request, Announcement $announcement)
    {
        if (!Auth::check()) return redirect()->route('login');
        
        $filePath = $announcement->file_path;
        if (empty($filePath)) {
            $fileName = Str::slug($announcement->title) . ".txt";
            $txtContent = "TITLE: " . $announcement->title . "\r\nDATE: " . $announcement->created_at->format('F d, Y') . "\r\n----------------\r\n\r\n" . $announcement->content;
            return response($txtContent, 200, ['Content-Type' => 'text/plain', 'Content-Disposition' => 'inline; filename="' . $fileName . '"']);
        }
        return response()->file(storage_path('app/public/' . $filePath));
    }

    public function destroyFile(Request $request)
    {
        $id = $request->input('id');
        $announcement = Announcement::findOrFail($id);
        if ($announcement->file_path) {
            $sharedCount = Announcement::where('file_path', $announcement->file_path)->where('id', '!=', $id)->count();
            if ($sharedCount === 0 && Storage::disk('public')->exists($announcement->file_path)) {
                Storage::disk('public')->delete($announcement->file_path);
            }
        }
        RecentActivity::create(['user_id' => Auth::id(), 'file_name' => basename($announcement->file_path) ?? $announcement->title, 'office_name' => 'Folder', 'action' => 'Deleted File']);
        $announcement->delete();
        return back()->with('success', 'File removed successfully.');
    }
}