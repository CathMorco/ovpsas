<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed the 7 Core OVPSAS Offices
        $offices = [
            ['office_code' => 'ARCDO', 'office_name' => 'Alumni Relations and Career Development Office'],
            ['office_code' => 'OCPS', 'office_name' => 'Office of the Counseling and Psychological Services'],
            ['office_code' => 'OSFA', 'office_name' => 'Office of Scholarship and Financial Assistance'],
            ['office_code' => 'OSS', 'office_name' => 'Office of the Student Services'],
            ['office_code' => 'OUR', 'office_name' => 'Office of the University Registrar'],
            ['office_code' => 'SDPO', 'office_name' => 'Sports Development Program Office'],
            ['office_code' => 'UCCA', 'office_name' => 'University Center for Culture and the Arts'],
        ];

        foreach ($offices as $office) {
            Office::create($office);
        }

        // 2. Create the Super Admin Account
        // Note: Make sure the column names match your User model exactly
        User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@sasis.pup.edu.ph',
            'password_hash' => Hash::make('AdminSASIS2026!'), // The initial password IT will use
            'role' => 'Super Admin', 
            'office_id' => 4, // Assigning to OSS (or whichever ID makes sense as the master office)
        ]);
    }
}
