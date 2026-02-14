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
        Schema::create('recent_activities', function (Blueprint $table) {
            $table->id();
            // Links the activity to a specific user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Stores details about the file/action
            $table->string('file_name');
            $table->string('office_name')->nullable();
            $table->string('action'); // e.g., 'Opened', 'Uploaded'
            
            $table->timestamps(); // Created_at and Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recent_activities');
    }
};
