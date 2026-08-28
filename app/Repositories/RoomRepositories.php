<?php

namespace App\Repositories;

use App\Models\Room;
use App\Models\Ward;

class RoomRepositories
{
    public function list($filter = [])
    {
        $room = Room::with(['ward.floor.building'])->orderBy('id', 'desc');

        if (!empty($filter['ward_pid'])) {
            $room->whereHas('ward', function ($q) use ($filter) {
                $q->where('pid', $filter['ward_pid']);
            });
        }

        return api_list($room, $filter, ['room_number', 'room_type', 'ward.name', 'ward.code']);
    }

    public function searchByPid($pid)
    {
        try {
            $room = Room::with(['ward.floor.building'])->where('pid', $pid)->first();

            if (!$room) {
                return [];
            }

            return $room;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveWardId($data);
            $room = Room::create($data);
            return $room->load('ward.floor.building');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($room_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveWardId($data);
            $room = Room::findOrFail($room_id);
            $room->update($data);

            return $room->load('ward.floor.building');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    private function resolveWardId($data)
    {
        if (!empty($data['ward_pid'])) {
            $data['ward_id'] = Ward::where('pid', $data['ward_pid'])->firstOrFail()->id;
            unset($data['ward_pid']);
        }

        return $data;
    }
}
