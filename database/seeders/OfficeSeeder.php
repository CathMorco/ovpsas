<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office; // Ensure this Model exists!

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            ['name' => 'Office of the Vice President for Student Affairs & Services', 'code' => 'OSAS'],
            ['name' => 'Alumni Relations and Career Development Office', 'code' => 'ARCDO'],
            ['name' => 'Office of the Counseling and Psychological Services', 'code' => 'OCPS'],
            ['name' => 'Office of Scholarship and Financial Assistance', 'code' => 'OSFA'],
            ['name' => 'Office of the Student Services', 'code' => 'OSS'],
            ['name' => 'Office of the University Registrar', 'code' => 'OUR'],
            ['name' => 'Sports Development Program Office', 'code' => 'SDPO'],
            ['name' => 'University Center for Culture and the Arts', 'code' => 'UCCA'],
        ];

        foreach ($offices as $office) {
            \App\Models\Office::updateOrCreate(
                ['code' => $office['code']], 
                ['name' => $office['name']]
            );
        }
    }
}