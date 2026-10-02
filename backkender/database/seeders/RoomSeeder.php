<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $standard = RoomType::where('name', 'Standard Room')->first();
        $deluxe = RoomType::where('name', 'Deluxe Room')->first();
        $suite = RoomType::where('name', 'Executive Suite')->first();

        if ($standard) {
            foreach (range(101, 105) as $num) {
                Room::updateOrCreate(
                    ['room_number' => (string) $num],
                    ['room_type_id' => $standard->id, 'status' => 'available']
                );
            }
        }

        if ($deluxe) {
            foreach (range(201, 203) as $num) {
                Room::updateOrCreate(
                    ['room_number' => (string) $num],
                    ['room_type_id' => $deluxe->id, 'status' => 'available']
                );
            }
        }

        if ($suite) {
            foreach (['301', '302'] as $num) {
                Room::updateOrCreate(
                    ['room_number' => $num],
                    ['room_type_id' => $suite->id, 'status' => 'available']
                );
            }
        }

        $this->command->info('Rooms seeded: ' . Room::count());
    }
}
