<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $roomsByWard = [
            'ER' => [
                ['room_number' => 'ER-TRIAGE', 'room_type' => 'Triage'],
                ['room_number' => 'ER-BAY-1', 'room_type' => 'Treatment Bay'],
            ],
            'MED' => [
                ['room_number' => 'MED-101', 'room_type' => 'Private'],
                ['room_number' => 'MED-102', 'room_type' => 'Semi-Private'],
                ['room_number' => 'MED-103', 'room_type' => 'Ward'],
            ],
            'SUR' => [
                ['room_number' => 'SUR-201', 'room_type' => 'Private'],
                ['room_number' => 'SUR-202', 'room_type' => 'Ward'],
            ],
            'PED' => [
                ['room_number' => 'PED-301', 'room_type' => 'Ward'],
            ],
            'OB' => [
                ['room_number' => 'OB-401', 'room_type' => 'Semi-Private'],
            ],
            'ICU' => [
                ['room_number' => 'ICU-01', 'room_type' => 'Critical Care'],
            ],
            'OPD' => [
                ['room_number' => 'OPD-CONSULT-1', 'room_type' => 'Consultation'],
            ],
        ];

        foreach ($roomsByWard as $wardCode => $rooms) {
            $ward = Ward::where('code', $wardCode)->first();
            if (!$ward) {
                continue;
            }

            foreach ($rooms as $room) {
                Room::updateOrCreate(
                    ['ward_id' => $ward->id, 'room_number' => $room['room_number']],
                    ['room_type' => $room['room_type']]
                );
            }
        }
    }
}
