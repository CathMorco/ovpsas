<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            ['code' => 'OVPSAS',  'name' => 'Office of the Vice President for Student Affairs & Services'],
            ['code' => 'ARCDO', 'name' => 'Alumni Relations and Career Development Office'],
            ['code' => 'OCPS',  'name' => 'Office of the Counseling and Psychological Services'],
            ['code' => 'OSFA',  'name' => 'Office of Scholarship and Financial Assistance'],
            ['code' => 'OSS',   'name' => 'Office of the Student Services'],
            ['code' => 'OUR',   'name' => 'Office of the University Registrar'],
            ['code' => 'SDPO',  'name' => 'Sports Development Program Office'],
            ['code' => 'UCCA',  'name' => 'University Center for Culture and the Arts'],
        ];

        foreach ($offices as $office) {
            Office::updateOrCreate(
                ['code' => $office['code']], 
                ['name' => $office['name']]
            );
        }
    }
}