<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentActivity extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_name', 'office_name', 'action'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}