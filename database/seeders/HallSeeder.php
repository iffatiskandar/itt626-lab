<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hall;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $halls = [
            ['lecture_hall_name' => 'DS', 'lecture_hall_place' => 'Dewan Seminar'],
            ['lecture_hall_name' => 'BT', 'lecture_hall_place' => 'Bilik Tutorial'],
            ['lecture_hall_name' => 'DK', 'lecture_hall_place' => 'Dewan Kuliah'],
            ['lecture_hall_name' => 'MK', 'lecture_hall_place' => 'Makmal Komputer'],
            ['lecture_hall_name' => 'MB', 'lecture_hall_place' => 'Makmal Biometrik'],
        ];

        foreach ($halls as $hall) {
            Hall::firstOrCreate(['lecture_hall_name' => $hall['lecture_hall_name']], $hall);
        }
    }
}
