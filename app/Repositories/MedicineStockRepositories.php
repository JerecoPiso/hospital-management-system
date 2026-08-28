<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Models\MedicineStock;

class MedicineStockRepositories
{
    public function list($filter = [])
    {
        $medicineStock = MedicineStock::with(['medicine'])->orderBy('id', 'desc');

        if (!empty($filter['medicine_pid'])) {
            $medicineStock->whereHas('medicine', function ($q) use ($filter) {
                $q->where('pid', $filter['medicine_pid']);
            });
        }

        return api_list($medicineStock, $filter, ['batch_number', 'unit_type', 'medicine.name', 'medicine.brand_name']);
    }

    public function searchByPid($pid)
    {
        try {
            $medicineStock = MedicineStock::with(['medicine'])->where('pid', $pid)->first();

            if (!$medicineStock) {
                return [];
            }

            return $medicineStock;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveMedicineId($data);
            $medicineStock = MedicineStock::create($data);
            return $medicineStock->load('medicine');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($medicine_stock_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveMedicineId($data);
            $medicineStock = MedicineStock::findOrFail($medicine_stock_id);
            $medicineStock->update($data);

            return $medicineStock->load('medicine');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    private function resolveMedicineId($data)
    {
        if (!empty($data['medicine_pid'])) {
            $data['medicine_id'] = Medicine::where('pid', $data['medicine_pid'])->firstOrFail()->id;
            unset($data['medicine_pid']);
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
