<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CREATE OFFICES
        $offices = [
            ['code' => 'ARCDO', 'name' => 'Alumni Relations and Career Development Office'],
            ['code' => 'OCPS',  'name' => 'Office of the Counseling and Psychological Services'],
            ['code' => 'OSFA',  'name' => 'Office of Scholarship and Financial Assistance'],
            ['code' => 'OSS',   'name' => 'Office of the Student Services'],
            ['code' => 'OUR',   'name' => 'Office of the University Registrar'],
            ['code' => 'SDPO',  'name' => 'Sports Development Program Office'],
            ['code' => 'UCCA',  'name' => 'University Center for Culture and the Arts'],
        ];

        foreach ($offices as $office) {
            Office::create($office);
        }

        // 2. CREATE USERS (5 Total)
        
        // User 1: Super Admin (The Ultimate Account)
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@sasis.edu', // <-- Unique Email
            'password' => Hash::make('password123'),
            'role' => 'Super Admin', 
            'office_id' => null,
            'designation' => 'System Architect',
            'status' => 'active', 
        ]);

        // User 2: System Admin (Regular Admin)
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@sasis.edu', // <-- Unique Email
            'password' => Hash::make('password123'),
            'role' => 'Admin', 
            'office_id' => null,
            'designation' => 'IT Administrator',
            'status' => 'active', 
        ]);

        // User 3: Office Staff (Linked to Office ID 1, which is ARCDO)
        User::create([
            'name' => 'ARCDO Staff',
            'email' => 'staff@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Office Staff',
            'office_id' => 1, 
            'designation' => 'Office Clerk',
            'status' => 'active',
        ]);

        // User 4: Viewer
        User::create([
            'name' => 'Guest Viewer',
            'email' => 'viewer@sasis.edu',
            'password' => Hash::make('password123'),
            'role' => 'Viewer',
            'office_id' => null,
            'designation' => 'Student/Guest',
            'status' => 'active',
        ]);

        // User 5: Test User
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Viewer',
            'office_id' => null,
            'designation' => 'Tester',
            'status' => 'active',
        ]);
    }
}