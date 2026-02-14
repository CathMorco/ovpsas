<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Create the OFFICES table first
        // This stores the master list of offices (ARCDO, OSS, etc.)
        if (!Schema::hasTable('offices')) {
            Schema::create('offices', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable(); // e.g., 'OSS'
                $table->timestamps();
            });
        }

        // 2. Create the ANNOUNCEMENTS table second
        // This stores your files and posts
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                
                // Links the post to the User who uploaded it
                $table->foreignId('user_id')->constrained()->onDelete('cascade');

                // FIXED: Changed from 'json' to 'string' to match your Model and Controller
                $table->string('office');   
                $table->string('category'); 

                $table->string('title')->nullable();
                $table->text('content')->nullable(); // Made nullable just in case
                $table->string('file_path')->nullable(); 
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order (Announcements first, then Offices)
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('offices');
    }
};