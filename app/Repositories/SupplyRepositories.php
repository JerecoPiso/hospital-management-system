<?php

namespace App\Repositories;

use App\Models\Supply;

class SupplyRepositories
{
    public function list($filter = [])
    {
        $query = Supply::withSum('supplyStocks', 'quantity')->orderBy('id', 'desc');

        return api_list($query, $filter, ['name', 'unit']);
    }

    public function searchByPid($pid)
    {
        try {
            $supply = Supply::where('pid', $pid)->first();

            if (!$supply) {
                return [];
            }

            return $supply;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $supply = Supply::create($data);
            return $supply;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($supply_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $supply = Supply::findOrFail($supply_id);
            $supply->update($data);

            return $supply;
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
