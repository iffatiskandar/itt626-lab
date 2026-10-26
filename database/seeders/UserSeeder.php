<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Ali', 'age' => '20', 'email' => 'ali@example.com', 'password' => Hash::make('password'), 'phone_number' => '0123456789', 'address' => 'Kuala Lumpur'],
            ['name' => 'Siti', 'age' => '21', 'email' => 'siti@example.com', 'password' => Hash::make('password'), 'phone_number' => '0134567890', 'address' => 'Shah Alam'],
            ['name' => 'Ahmad', 'age' => '22', 'email' => 'ahmad@example.com', 'password' => Hash::make('password'), 'phone_number' => '0145678901', 'address' => 'Petaling Jaya'],
            ['name' => 'Maya', 'age' => '21', 'email' => 'maya@example.com', 'password' => Hash::make('password'), 'phone_number' => '0156789012', 'address' => 'Subang Jaya'],
            ['name' => 'Hafiz', 'age' => '23', 'email' => 'hafiz@example.com', 'password' => Hash::make('password'), 'phone_number' => '0167890123', 'address' => 'Cyberjaya'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }

    }
}
