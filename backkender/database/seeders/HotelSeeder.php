<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Receptionist',
            'email' => 'staff@hotel.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
        ]);

        User::create([
            'name' => 'Test Guest',
            'email' => 'guest@hotel.com',
            'password' => Hash::make('password'),
            'role' => 'guest',
        ]);

        // Room Types
        $standard = RoomType::create([
            'name' => 'Standard Room',
            'price' => 350000,
            'description' => 'Kamar nyaman dengan fasilitas dasar lengkap.',
            'facilities' => 'TV, AC, WiFi, Kamar Mandi Dalam',
        ]);

        $deluxe = RoomType::create([
            'name' => 'Deluxe Room',
            'price' => 600000,
            'description' => 'Kamar luas dengan pemandangan kota dan fasilitas premium.',
            'facilities' => 'TV 4K, AC, WiFi, Kamar Mandi Dalam, Bathtub, Coffee Maker',
        ]);

        $suite = RoomType::create([
            'name' => 'Executive Suite',
            'price' => 1200000,
            'description' => 'Pengalaman menginap termewah dengan ruang tamu terpisah.',
            'facilities' => 'TV 4K, AC, WiFi, Bathtub, Kitchenette, Mini Bar, Balkon Pribadi',
        ]);

        // Rooms
        foreach (range(101, 105) as $num) {
            Room::create(['room_number' => (string)$num, 'room_type_id' => $standard->id, 'status' => 'available']);
        }

        foreach (range(201, 203) as $num) {
            Room::create(['room_number' => (string)$num, 'room_type_id' => $deluxe->id, 'status' => 'available']);
        }

        Room::create(['room_number' => '301', 'room_type_id' => $suite->id, 'status' => 'available']);
    }
}
