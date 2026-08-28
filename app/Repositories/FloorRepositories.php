<?php

namespace App\Repositories;

use App\Models\Building;
use App\Models\Floor;

class FloorRepositories
{
    public function list($filter = [])
    {
        $floor = Floor::with(['building'])->orderBy('id', 'desc');

        if (!empty($filter['building_pid'])) {
            $floor->whereHas('building', function ($q) use ($filter) {
                $q->where('pid', $filter['building_pid']);
            });
        }

        return api_list($floor, $filter, ['floor_number', 'name', 'description', 'building.name', 'building.code']);
    }

    public function searchByPid($pid)
    {
        try {
            $floor = Floor::with(['building'])->where('pid', $pid)->first();

            if (!$floor) {
                return [];
            }

            return $floor;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveBuildingId($data);
            $floor = Floor::create($data);
            return $floor->load('building');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($floor_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveBuildingId($data);
            $floor = Floor::findOrFail($floor_id);
            $floor->update($data);

            return $floor->load('building');
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

    private function resolveBuildingId($data)
    {
        if (!empty($data['building_pid'])) {
            $data['building_id'] = Building::where('pid', $data['building_pid'])->firstOrFail()->id;
            unset($data['building_pid']);
        }

        return $data;
    }
}
