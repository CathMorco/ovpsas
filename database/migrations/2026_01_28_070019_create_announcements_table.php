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
            // Connects the announcement to the user who posted it
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('office'); // Stores: ARCDO, OCPS, OSFA, etc.
            $table->string('title');
            $table->text('content');

            // stores the file path (e.g., 'offices/ARCDO/document.pdf')
            // nullable() because you said it's okay if they upload no file
            $table->string('file_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
