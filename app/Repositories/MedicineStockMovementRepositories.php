<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Models\MedicineStockMovement;

class MedicineStockMovementRepositories
{
    public function list($filter = [])
    {
        $medicineStockMovement = MedicineStockMovement::with(['medicine'])->orderBy('id', 'desc');

        if (!empty($filter['medicine_pid'])) {
            $medicineStockMovement->whereHas('medicine', function ($q) use ($filter) {
                $q->where('pid', $filter['medicine_pid']);
            });
        }

        $medicineStockMovement = $medicineStockMovement->get();
        return $medicineStockMovement->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $medicineStockMovement = MedicineStockMovement::with(['medicine'])->where('pid', $pid)->first();

            if (!$medicineStockMovement) {
                return [];
            }

            return $medicineStockMovement;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $medicine = Medicine::where('pid', $data['medicine_pid'])->firstOrFail();
            $data['medicine_id'] = $medicine->id;
            unset($data['medicine_pid']);

            $medicineStockMovement = MedicineStockMovement::create($data);

            return $medicineStockMovement->load('medicine');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
