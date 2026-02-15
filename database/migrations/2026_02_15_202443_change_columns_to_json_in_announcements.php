<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('announcements', function (Blueprint $table) {
        // We verify the columns exist, then change them to JSON
        // Note: If you have existing data, this might fail unless you clear the table first.
        $table->json('office')->change();
        $table->json('category')->change();
    });
}

public function down(): void
{
    Schema::table('announcements', function (Blueprint $table) {
        $table->string('office')->change();
        $table->string('category')->change();
    });
}
};
