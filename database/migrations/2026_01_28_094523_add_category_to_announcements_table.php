<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // This adds the 'category' column after the 'office' column.
            // We make it 'nullable' so your old posts don't break.
            $table->string('category')->nullable()->after('office');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // This removes the column if you ever need to roll back the database.
            $table->dropColumn('category');
        });
    }
};
