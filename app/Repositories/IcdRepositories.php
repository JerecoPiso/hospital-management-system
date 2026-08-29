<?php

namespace App\Repositories;

use App\Models\Icd;

class IcdRepositories
{
    public function list($filter = [])
    {
        $query = Icd::orderBy('id', 'asc');

        return api_list($query, $filter, ['code', 'name']);
    }

    public function searchByPid($pid)
    {
        try {
            $icd = Icd::where('pid', $pid)->first();

            if (!$icd) {
                return [];
            }

            return $icd;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return Icd::create($data);
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

            $icd = Icd::findOrFail($id);
            $icd->update($data);

            return $icd;
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
