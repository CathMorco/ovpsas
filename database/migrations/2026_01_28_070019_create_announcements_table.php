<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Core fields
            $table->json('office'); 
            $table->json('category');
            $table->string('title');
            
            // The "Optional" fields 
            $table->text('content')->nullable(); 
            $table->text('file_path')->nullable(); // Handles JSON arrays of multiple files
            $table->string('custom_category')->nullable();
            
            // Added fields
            $table->timestamp('scheduled_date')->nullable(); 
            $table->text('link')->nullable(); // Handles long external/meeting URLs
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};