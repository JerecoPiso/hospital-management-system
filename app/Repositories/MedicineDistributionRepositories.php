<?php

namespace App\Repositories;

use App\Models\MedicineDistribution;
use App\Models\MedicineStock;
use App\Models\MedicineStockMovement;
use App\Models\Station;
use Illuminate\Support\Facades\DB;

class MedicineDistributionRepositories
{
    public function list($filter = [])
    {
        $medicineDistribution = MedicineDistribution::with(['medicineStock.medicine', 'station', 'distributedBy'])->orderBy('id', 'desc');

        if (!empty($filter['medicine_stock_pid'])) {
            $medicineDistribution->whereHas('medicineStock', function ($q) use ($filter) {
                $q->where('pid', $filter['medicine_stock_pid']);
            });
        }

        $medicineDistribution = $medicineDistribution->get();
        return $medicineDistribution->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $medicineDistribution = MedicineDistribution::with(['medicineStock.medicine', 'station', 'distributedBy'])->where('pid', $pid)->first();

            if (!$medicineDistribution) {
                return [];
            }

            return $medicineDistribution;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $medicineStock = MedicineStock::where('pid', $data['medicine_stock_pid'])->lockForUpdate()->firstOrFail();
                $station = Station::where('pid', $data['station_pid'])->firstOrFail();

                if ($medicineStock->quantity < $data['quantity']) {
                    throw new \Exception("Insufficient stock: only {$medicineStock->quantity} unit(s) available.");
                }

                $medicineStock->quantity -= $data['quantity'];
                $medicineStock->save();

                $data['medicine_stock_id'] = $medicineStock->id;
                $data['station_id'] = $station->id;
                unset($data['medicine_stock_pid'], $data['station_pid']);

                $data['distributed_by'] ??= auth()->id();
                $data['distributed_at'] ??= now();

                $medicineDistribution = MedicineDistribution::create($data);

                MedicineStockMovement::create([
                    'medicine_id' => $medicineStock->medicine_id,
                    'type' => 'OUT',
                    'quantity' => $data['quantity'],
                    'reference' => $medicineDistribution->pid,
                    'remarks' => 'Distributed to station',
                ]);

                return $medicineDistribution->load(['medicineStock.medicine', 'station', 'distributedBy']);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
