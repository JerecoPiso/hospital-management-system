<?php

namespace App\Repositories;

use App\Models\Diet;

class DietRepositories
{
    public function list($filter = [])
    {
        $query = Diet::orderBy('id', 'desc');

        return api_list($query, $filter, ['name', 'description']);
    }

    public function searchByPid($pid)
    {
        try {
            $diet = Diet::where('pid', $pid)->first();

            if (!$diet) {
                return [];
            }

            return $diet;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $diet = Diet::create($data);
            return $diet;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($diet_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $diet = Diet::findOrFail($diet_id);
            $diet->update($data);

            return $diet;
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
