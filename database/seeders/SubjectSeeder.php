<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['subject_code' => 'ITT602', 'subject_name' => 'Mobile Development', 'lecturer_name' => 'Madam Ida'],
            ['subject_code' => 'ITT626', 'subject_name' => 'Back-End Technology', 'lecturer_name' => 'Sir Asrol'],
            ['subject_code' => 'ITT593', 'subject_name' => 'Digital Forensics', 'lecturer_name' => 'Ts. Albin'],
            ['subject_code' => 'ITT550', 'subject_name' => 'Networking Management', 'lecturer_name' => 'Sir Anwar'],
            ['subject_code' => 'ITT588', 'subject_name' => 'Front-End Technology', 'lecturer_name' => 'Dr. Aimuni'],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['subject_code' => $subject['subject_code']], $subject);
        }
    }
}
