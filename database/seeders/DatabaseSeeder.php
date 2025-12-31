<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@office.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create intern users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@office.com',
            'password' => bcrypt('password'),
            'role' => 'intern',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@office.com',
            'password' => bcrypt('password'),
            'role' => 'intern',
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@office.com',
            'password' => bcrypt('password'),
            'role' => 'intern',
        ]);
    }
}
