<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $announcementId)
    {
        // 1. Validate the comment text
        $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        // 2. Create the comment
        Comment::create([
            'user_id' => Auth::id(),
            'announcement_id' => $announcementId,
            'comment_text' => $request->comment_text,
        ]);

        // 3. Redirect back to the dashboard with a success message
        return back()->with('success', 'Comment posted successfully!');
    }
}
