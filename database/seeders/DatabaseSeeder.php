<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. RUN OFFICE SEEDER FIRST
        // This creates the offices (ARCDO, OSS, etc.) so we can assign users to them.
        $this->call([
            OfficeSeeder::class,
        ]);
        
        // 2. CREATE USERS
        
        // System Administrator
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'office_id' => null,
            'designation' => 'IT Administrator',
        ]);

        // Office Staff (Linked to Office ID 1, which now exists!)
        User::factory()->create([
            'name' => 'ARCDO Staff',
            'email' => 'staff@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Office Staff',
            'office_id' => 1, 
            'designation' => 'Office Clerk',
        ]);

        // Viewer
        User::factory()->create([
            'name' => 'Guest Viewer',
            'email' => 'viewer@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Viewer',
            'office_id' => null,
            'designation' => 'Student/Guest',
        ]);
        
        // Test User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'Viewer',
        ]);
    }
}