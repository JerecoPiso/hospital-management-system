<?php

namespace App\Repositories;

use App\Models\Building;

class BuildingRepositories
{
    public function list($filter = [])
    {
        $building = Building::orderBy('id', 'desc');
        $building = $building->get();
        return $building->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $building = Building::where('pid', $pid)->first();

            if (!$building) {
                return [];
            }

            return $building;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $building = Building::create($data);
            return $building;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($building_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $building = Building::findOrFail($building_id);
            $building->update($data);

            return $building;
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
}
