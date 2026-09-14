<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\MedicineStockMovement;

class MedicineStockMovementRepositories
{
    public function list($filter = [])
    {
        $medicineStockMovement = MedicineStockMovement::with(['medicineStock', 'medicineStock.medicine'])->orderBy('id', 'desc');

        if (!empty($filter['medicine_pid'])) {
            $medicineStockMovement->whereHas('medicineStock', function ($q) use ($filter) {
                $q->where('pid', $filter['medicine_pid']);
            });
        }

        return api_list($medicineStockMovement, $filter, ['type', 'reference', 'remarks', 'medicineStock.medicine.name']);
    }

    public function searchByPid($pid)
    {
        try {
            $medicineStockMovement = MedicineStockMovement::with(['medicineStock'])->where('pid', $pid)->first();

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
            $medicine = MedicineStock::where('pid', $data['medicine_stock_pid'])->firstOrFail();
            $data['medicine_stock_id'] = $medicine->id;
            unset($data['medicine_stock_pid']);

            $medicineStockMovement = MedicineStockMovement::create($data);

            return $medicineStockMovement->load('medicineStoc.medicine');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
