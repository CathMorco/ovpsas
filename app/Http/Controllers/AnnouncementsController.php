<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'office',
        'category',
        'title',
        'content',
        'file_path',
    ];

    /**
     * The attributes that should be cast.
     * * CRITICAL: This allows your Controller to save ['OSS', 'ARCDO'] directly.
     * Laravel will automatically convert it to JSON format ["OSS","ARCDO"] for the database.
     */
    protected $casts = [
        'office' => 'array',
        'category' => 'array',
    ];

    /**
     * Get the user that authored the announcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the announcement.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}