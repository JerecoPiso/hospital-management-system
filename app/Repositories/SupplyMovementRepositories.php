<?php

namespace App\Repositories;

use App\Models\SupplyMovement;
use App\Models\SupplyStock;
use Illuminate\Support\Facades\DB;

class SupplyMovementRepositories
{
    public function list($filter = [])
    {
        $supplyMovement = SupplyMovement::with(['supplyStock.supply'])->orderBy('id', 'desc');

        if (!empty($filter['supply_stock_pid'])) {
            $supplyMovement->whereHas('supplyStock', function ($q) use ($filter) {
                $q->where('pid', $filter['supply_stock_pid']);
            });
        }

        $supplyMovement = $supplyMovement->get();
        return $supplyMovement->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $supplyMovement = SupplyMovement::with(['supplyStock.supply'])->where('pid', $pid)->first();

            if (!$supplyMovement) {
                return [];
            }

            return $supplyMovement;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $supplyStock = SupplyStock::where('pid', $data['supply_stock_pid'])->lockForUpdate()->firstOrFail();

                if ($data['type'] === 'OUT' && $supplyStock->quantity < $data['quantity']) {
                    throw new \Exception("Insufficient stock: only {$supplyStock->quantity} unit(s) available.");
                }

                $supplyStock->quantity += $data['type'] === 'IN' ? $data['quantity'] : -$data['quantity'];
                $supplyStock->save();

                $data['supply_stock_id'] = $supplyStock->id;
                unset($data['supply_stock_pid']);

                $supplyMovement = SupplyMovement::create($data);

                return $supplyMovement->load('supplyStock.supply');
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
