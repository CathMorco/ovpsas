<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    // 1. Tell Laravel this model uses the 'sessions' table
    protected $table = 'sessions';

    // 2. The 'id' is a string (not an integer)
    public $incrementing = false;
    protected $keyType = 'string';

    // 3. Disable timestamps (sessions table handles its own time)
    public $timestamps = false;
}