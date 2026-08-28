<?php

namespace App\Repositories;

use App\Models\Station;
use App\Models\SupplyDistribution;
use App\Models\SupplyMovement;
use App\Models\SupplyStock;
use Illuminate\Support\Facades\DB;

class SupplyDistributionRepositories
{
    public function list($filter = [])
    {
        $supplyDistribution = SupplyDistribution::with(['supplyStock.supply', 'station', 'distributedBy'])->orderBy('id', 'desc');

        if (!empty($filter['supply_stock_pid'])) {
            $supplyDistribution->whereHas('supplyStock', function ($q) use ($filter) {
                $q->where('pid', $filter['supply_stock_pid']);
            });
        }

        return api_list($supplyDistribution, $filter, ['supplyStock.supply.name', 'station.name']);
    }

    public function searchByPid($pid)
    {
        try {
            $supplyDistribution = SupplyDistribution::with(['supplyStock.supply', 'station', 'distributedBy'])->where('pid', $pid)->first();

            if (!$supplyDistribution) {
                return [];
            }

            return $supplyDistribution;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $supplyStock = SupplyStock::where('pid', $data['supply_stock_pid'])->lockForUpdate()->firstOrFail();
                $station = Station::where('pid', $data['station_pid'])->firstOrFail();

                if ($supplyStock->quantity < $data['quantity']) {
                    throw new \Exception("Insufficient stock: only {$supplyStock->quantity} unit(s) available.");
                }

                $supplyStock->quantity -= $data['quantity'];
                $supplyStock->save();

                $data['supply_stock_id'] = $supplyStock->id;
                $data['station_id'] = $station->id;
                unset($data['supply_stock_pid'], $data['station_pid']);

                $data['distributed_by'] ??= auth()->id();
                $data['distributed_at'] ??= now();

                $supplyDistribution = SupplyDistribution::create($data);

                SupplyMovement::create([
                    'supply_stock_id' => $supplyStock->id,
                    'quantity' => $data['quantity'],
                    'type' => 'OUT',
                    'used_for' => 'Distributed to station',
                ]);

                return $supplyDistribution->load(['supplyStock.supply', 'station', 'distributedBy']);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
