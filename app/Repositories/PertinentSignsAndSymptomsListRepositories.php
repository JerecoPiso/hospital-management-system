<?php

namespace App\Repositories;

use App\Models\PertinentSignsAndSymptomsList;

class PertinentSignsAndSymptomsListRepositories
{
    public function list($filter = [])
    {
        $query = PertinentSignsAndSymptomsList::orderBy('id', 'asc');

        return api_list($query, $filter, ['code', 'name', 'others']);
    }

    public function searchByPid($pid)
    {
        try {
            $item = PertinentSignsAndSymptomsList::where('pid', $pid)->first();

            if (!$item) {
                return [];
            }

            return $item;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return PertinentSignsAndSymptomsList::create($data);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $item = PertinentSignsAndSymptomsList::findOrFail($id);
            $item->update($data);

            return $item;
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
