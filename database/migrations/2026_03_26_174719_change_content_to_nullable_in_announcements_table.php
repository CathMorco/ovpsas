<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // This modifies the existing column to allow NULL values
            $table->text('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // This reverts it back to NOT NULL if you roll back
            $table->text('content')->nullable(false)->change();
        });
    }
};