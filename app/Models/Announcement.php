<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    // ADDED 'link' to the array
    protected $fillable = [
        'user_id', 'title', 'content', 'office', 'category', 
        'scheduled_date', 'file_path', 'custom_category', 'link'
    ];

    protected $casts = [
        'office' => 'array',
        'category' => 'array',
        'scheduled_date' => 'date', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}