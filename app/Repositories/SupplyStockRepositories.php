<?php

namespace App\Repositories;

use App\Models\Supply;
use App\Models\SupplyStock;

class SupplyStockRepositories
{
    public function list($filter = [])
    {
        $supplyStock = SupplyStock::with(['supply'])->orderBy('id', 'desc');

        if (!empty($filter['supply_pid'])) {
            $supplyStock->whereHas('supply', function ($q) use ($filter) {
                $q->where('pid', $filter['supply_pid']);
            });
        }

        $supplyStock = $supplyStock->get();
        return $supplyStock->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $supplyStock = SupplyStock::with(['supply'])->where('pid', $pid)->first();

            if (!$supplyStock) {
                return [];
            }

            return $supplyStock;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveSupplyId($data);
            $supplyStock = SupplyStock::create($data);
            return $supplyStock->load('supply');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($supply_stock_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveSupplyId($data);
            $supplyStock = SupplyStock::findOrFail($supply_stock_id);
            $supplyStock->update($data);

            return $supplyStock->load('supply');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    private function resolveSupplyId($data)
    {
        if (!empty($data['supply_pid'])) {
            $data['supply_id'] = Supply::where('pid', $data['supply_pid'])->firstOrFail()->id;
            unset($data['supply_pid']);
        }

        return $data;
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
