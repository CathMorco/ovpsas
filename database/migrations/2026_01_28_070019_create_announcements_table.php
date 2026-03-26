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
            $table->string('office'); 
            $table->string('category');
            $table->string('title');
            
            // The "Optional" fields - Ensure these are clean!
            $table->text('content')->nullable(); 
            $table->string('file_path')->nullable();
            $table->string('custom_category')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
