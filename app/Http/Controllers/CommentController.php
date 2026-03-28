<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Announcement;
use App\Models\RecentActivity; // <-- ADDED
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $announcementId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'announcement_id' => $announcementId,
            'comment_text' => $request->comment_text,
        ]);

        // LOG THE COMMENT IN THE ACTIVITY FEED
        $announcement = Announcement::find($announcementId);
        if ($announcement) {
            $offices = is_array($announcement->office) ? implode(', ', $announcement->office) : $announcement->office;
            RecentActivity::create([
                'user_id'     => Auth::id(),
                'file_name'   => $announcement->title, // Track the post title
                'office_name' => $offices,
                'action'      => 'Commented'
            ]);
        }

        return back()->with('success', 'Comment posted successfully!');
    }
}