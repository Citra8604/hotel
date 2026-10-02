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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Hotel',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Resepsionis Hotel',
            'email' => 'resepsionis@hotel.com',
            'password' => bcrypt('resepsionis123'),
            'role' => 'receptionist',
        ]);

        User::factory()->create([
            'name' => 'Tamu Hotel',
            'email' => 'tamu@hotel.com',
            'password' => bcrypt('tamu123'),
            'role' => 'guest',
        ]);
    }
}
