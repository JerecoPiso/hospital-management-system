<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\MedicineStockMovement;
use Illuminate\Support\Facades\DB;

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

    /**
     * The opening quantity of a new batch is logged as an IN movement so the
     * movement ledger accounts for every unit that enters stock.
     */
    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $data = $this->resolveMedicineId($data);
                $medicineStock = MedicineStock::create($data);

                $this->recordMovement($medicineStock, (int) $medicineStock->quantity, 'Stock added' . ($medicineStock->batch_number ? " (batch {$medicineStock->batch_number})" : ''));

                return $medicineStock->load('medicine');
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * A manual quantity edit is logged as an IN (increase) or OUT (decrease)
     * adjustment for the difference, keeping the ledger in step with the batch.
     */
    public function update($medicine_stock_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            return DB::transaction(function () use ($medicine_stock_id, $data) {
                $data = $this->resolveMedicineId($data);
                $medicineStock = MedicineStock::lockForUpdate()->findOrFail($medicine_stock_id);
                $previousQuantity = (int) $medicineStock->quantity;

                $medicineStock->update($data);

                $this->recordMovement($medicineStock, (int) $medicineStock->quantity - $previousQuantity, 'Stock adjusted' . ($medicineStock->batch_number ? " (batch {$medicineStock->batch_number})" : ''));

                return $medicineStock->load('medicine');
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * IN movements carry the batch's purchase price. OUT adjustments are priced
     * at 0 so they don't count as dispensed income on the movements page.
     */
    private function recordMovement(MedicineStock $medicineStock, int $change, string $remarks)
    {
        if ($change === 0) {
            return;
        }

        MedicineStockMovement::create([
            'medicine_stock_id' => $medicineStock->id,
            'type' => $change > 0 ? 'IN' : 'OUT',
            'quantity' => abs($change),
            'price' => $change > 0 ? ($medicineStock->purchase_price ?? 0) : 0,
            'reference' => $medicineStock->batch_number,
            'remarks' => $remarks,
        ]);
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
