<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code']; // Add other columns if you have them

    // Relationship: One Office has many Users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}