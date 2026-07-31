<?php

namespace App\Repositories;

use App\Models\Station;
use App\Models\Ward;

class StationRepositories
{
    public function list($filter = [])
    {
        $station = Station::with(['ward.floor.building'])->orderBy('name', 'asc');

        if (!empty($filter['ward_pid'])) {
            $station->whereHas('ward', function ($q) use ($filter) {
                $q->where('pid', $filter['ward_pid']);
            });
        }

        $station = $station->get();
        return $station->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $station = Station::with(['ward.floor.building'])->where('pid', $pid)->first();

            if (!$station) {
                return [];
            }

            return $station;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveWardId($data);
            $station = Station::create($data);
            return $station->load('ward.floor.building');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($station_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveWardId($data);
            $station = Station::findOrFail($station_id);
            $station->update($data);

            return $station->load('ward.floor.building');
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
