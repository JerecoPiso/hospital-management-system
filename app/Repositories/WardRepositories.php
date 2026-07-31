<?php

namespace App\Repositories;

use App\Models\Floor;
use App\Models\Ward;

class WardRepositories
{
    public function list($filter = [])
    {
        $ward = Ward::with(['floor.building'])->orderBy('id', 'desc');

        if (!empty($filter['floor_pid'])) {
            $ward->whereHas('floor', function ($q) use ($filter) {
                $q->where('pid', $filter['floor_pid']);
            });
        }

        $ward = $ward->get();
        return $ward->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $ward = Ward::with(['floor.building'])->where('pid', $pid)->first();

            if (!$ward) {
                return [];
            }

            return $ward;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveFloorId($data);
            $ward = Ward::create($data);
            return $ward->load('floor.building');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($ward_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveFloorId($data);
            $ward = Ward::findOrFail($ward_id);
            $ward->update($data);

            return $ward->load('floor.building');
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

    private function resolveFloorId($data)
    {
        if (!empty($data['floor_pid'])) {
            $data['floor_id'] = Floor::where('pid', $data['floor_pid'])->firstOrFail()->id;
            unset($data['floor_pid']);
        }

        return $data;
    }
}
