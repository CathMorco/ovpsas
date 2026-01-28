<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. System Administrator - Full access to Announcements and Reports
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'office_id' => null,
            'designation' => 'IT Administrator',
        ]);

        // 2. Office Staff - Access to specific office folders (e.g., ARCDO)
        User::factory()->create([
            'name' => 'ARCDO Staff',
            'email' => 'staff@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Office Staff',
            'office_id' => 1, // Linked to Alumni Relations and Career Development Office
            'designation' => 'Office Clerk',
        ]);

        // 3. Viewer - Can search and download documents only
        User::factory()->create([
            'name' => 'Guest Viewer',
            'email' => 'viewer@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Viewer',
            'office_id' => null,
            'designation' => 'Student/Guest',
        ]);
    }
}