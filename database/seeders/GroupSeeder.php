<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'CS230', 'part' => '5'],
            ['name' => 'CS251', 'part' => '5'],
            ['name' => 'CS253', 'part' => '5'],
            ['name' => 'CS255', 'part' => '5'],
            ['name' => 'CS266', 'part' => '5'],
        ];

        foreach ($groups as $group) {
            Group::firstOrCreate(['name' => $group['name'], 'part' => $group['part']], $group);
        }
    }
}
