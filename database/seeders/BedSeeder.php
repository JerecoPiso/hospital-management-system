<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        $bedsByRoom = [
            'ER-BAY-1' => ['ER-BAY-1-A', 'ER-BAY-1-B'],
            'MED-101' => ['MED-101-A'],
            'MED-102' => ['MED-102-A', 'MED-102-B'],
            'MED-103' => ['MED-103-A', 'MED-103-B', 'MED-103-C', 'MED-103-D'],
            'SUR-201' => ['SUR-201-A'],
            'SUR-202' => ['SUR-202-A', 'SUR-202-B', 'SUR-202-C'],
            'PED-301' => ['PED-301-A', 'PED-301-B', 'PED-301-C'],
            'OB-401' => ['OB-401-A', 'OB-401-B'],
            'ICU-01' => ['ICU-01-01', 'ICU-01-02', 'ICU-01-03', 'ICU-01-04'],
        ];

        foreach ($bedsByRoom as $roomNumber => $beds) {
            $room = Room::where('room_number', $roomNumber)->first();
            if (!$room) {
                continue;
            }

            foreach ($beds as $bedNumber) {
                Bed::updateOrCreate(
                    ['room_id' => $room->id, 'bed_number' => $bedNumber],
                    ['status' => 'available']
                );
            }
        }
    }
}
