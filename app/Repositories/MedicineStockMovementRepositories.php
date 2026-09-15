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

        if (!empty($filter['type'])) {
            $medicineStockMovement->where('type', $filter['type']);
        }

        if (!empty($filter['date_from'])) {
            $medicineStockMovement->whereDate('created_at', '>=', $filter['date_from']);
        }

        if (!empty($filter['date_to'])) {
            $medicineStockMovement->whereDate('created_at', '<=', $filter['date_to']);
        }

        // Income (dispensed value) always reflects OUT movements within the same
        // medicine/date/search filters, regardless of the `type` filter above —
        // cloned before api_list() applies search + pagination to the main query.
        $incomeQuery = (clone $medicineStockMovement)->where('type', 'OUT');
        $search = trim((string) ($filter['search'] ?? ''));
        if ($search !== '') {
            $incomeQuery->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('medicineStock.medicine', function ($mq) use ($search) {
                        $mq->where('name', 'like', "%{$search}%");
                    });
            });
        }
        $totalIncome = (float) ($incomeQuery->selectRaw('COALESCE(SUM(price * quantity), 0) as total_income')->value('total_income') ?? 0);

        $result = api_list($medicineStockMovement, $filter, ['type', 'reference', 'remarks', 'medicineStock.medicine.name']);
        $result['meta']['total_income'] = $totalIncome;

        return $result;
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

            return $medicineStockMovement->load('medicineStock.medicine');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
