<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Office;
use App\Models\Session;
use App\Models\Announcement;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'suffix',
        'email',
        'phone',
        'password',
        'office_id',
        'designation', 
        'role',
        'avatar',
        'status',
        'last_seen_at', // Added for Quick Panel tracking
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime', // Ensures we can use Carbon methods
        ];
    }

    /**
     * Role-Based Access Control (RBAC) Helpers
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'Super Admin';
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'Office Staff';
    }

    public function isViewer(): bool
    {
        return $this->role === 'Viewer';
    }

    /**
     * Relationship with the Office
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Relationship with Sessions
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Relationship with Announcements (My Uploads)
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}