<?php

namespace App\Repositories;

use App\Models\RadiologyModality;

class RadiologyModalityRepositories
{
    public function list($filter = [])
    {
        $query = RadiologyModality::orderBy('id', 'desc');

        return api_list($query, $filter, ['code', 'name', 'room_number']);
    }

    public function searchByPid($pid)
    {
        try {
            $modality = RadiologyModality::where('pid', $pid)->first();

            if (!$modality) {
                return [];
            }

            return $modality;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $modality = RadiologyModality::create($data);
            return $modality;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($modality_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $modality = RadiologyModality::findOrFail($modality_id);
            $modality->update($data);

            return $modality;
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
