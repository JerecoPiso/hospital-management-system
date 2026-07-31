<?php

namespace App\Repositories;

use App\Models\Bed;
use App\Models\Room;

class BedRepositories
{
    public function list($filter = [])
    {
        $bed = Bed::with(['room.ward'])->orderBy('id', 'desc');

        if (!empty($filter['room_pid'])) {
            $bed->whereHas('room', function ($q) use ($filter) {
                $q->where('pid', $filter['room_pid']);
            });
        }

        $bed = $bed->get();
        return $bed->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $bed = Bed::with(['room.ward'])->where('pid', $pid)->first();

            if (!$bed) {
                return [];
            }

            return $bed;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveRoomId($data);
            $bed = Bed::create($data);
            return $bed->load('room.ward');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($bed_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveRoomId($data);
            $bed = Bed::findOrFail($bed_id);
            $bed->update($data);

            return $bed->load('room.ward');
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

    private function resolveRoomId($data)
    {
        if (!empty($data['room_pid'])) {
            $data['room_id'] = Room::where('pid', $data['room_pid'])->firstOrFail()->id;
            unset($data['room_pid']);
        }

        return $data;
    }
}
