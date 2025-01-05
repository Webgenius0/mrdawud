<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'Admin',
            'email' => 'admin@admin
            .com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'user',
            'email' => 'user@user.com',
            'password' => Hash::make('12345678'),
        ]);
        User::create([
            'username' => 'instructor',
            'email' => 'instructor@instructor.com',
            'password' => Hash::make('12345678'),
            'role' => 'instructor',
        ]);
    }
}
