<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Office;
use App\Models\Session;
use App\Models\Announcement; // <--- Critical Import for "My Uploads"

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'suffix',      // From Code 1 (Profile)
        'email',
        'phone',       // From Code 1 (Profile)
        'password',
        'office_id',
        'designation', 
        'role',        // RBAC
        'avatar',      // From Code 1 (Profile Picture)
        'status',      // From Code 2 (Login Approval System)
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
        ];
    }

    /**
     * Role-Based Access Control (RBAC) Helpers
     * We keep Capitalized versions to match your DatabaseSeeder.
     */
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
     * Get the sessions associated with the user.
     * REQUIRED for the Active Users sidebar.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Get the announcements (files) uploaded by this user.
     * REQUIRED for the Profile Page "My Uploads" list.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}