<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;

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
     * * This ensures 'office' and 'category' are handled as arrays
     * instead of just plain strings.
     */
    protected $casts = [
        'office' => 'array',
        'category' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
