<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Call the Office Seeder first so offices exist before users
        $this->call([
            OfficeSeeder::class,
        ]);

        // Dynamically fetch the ARCDO office ID to prevent hardcoded ID errors
        $arcdoOffice = Office::where('code', 'ARCDO')->first();

        // 2. Create the Master Admin Account
        User::firstOrCreate(
            ['email' => 'superadmin@sasis.edu'], // Prevents duplicating the admin if you run seeder twice
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'Super Admin', 
                'office_id' => null,
                'designation' => 'System Architect',
                'status' => 'active', 
            ]
        );

        // 3. Create System Admin (Regular Admin)
        User::firstOrCreate(
            ['email' => 'admin@sasis.edu'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role' => 'Admin', 
                'office_id' => null,
                'designation' => 'IT Administrator',
                'status' => 'active', 
            ]
        );

        // 4. Create Office Staff (Linked to ARCDO)
        User::firstOrCreate(
            ['email' => 'staff@sasis.edu'],
            [
                'name' => 'ARCDO Staff',
                'password' => Hash::make('password123'),
                'role' => 'Office Staff',
                'office_id' => $arcdoOffice ? $arcdoOffice->id : null, 
                'designation' => 'Office Clerk',
                'status' => 'active',
            ]
        );

        // 5. Create Guest Viewer
        User::firstOrCreate(
            ['email' => 'viewer@sasis.edu'],
            [
                'name' => 'Guest Viewer',
                'password' => Hash::make('password123'),
                'role' => 'Viewer',
                'office_id' => null,
                'designation' => 'Student/Guest',
                'status' => 'active',
            ]
        );

        // 6. Create Test User
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'role' => 'Viewer',
                'office_id' => null,
                'designation' => 'Tester',
                'status' => 'active',
            ]
        );
    }
}