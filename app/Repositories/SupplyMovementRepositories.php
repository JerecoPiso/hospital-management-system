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

        if (!empty($filter['type'])) {
            $supplyMovement->where('type', $filter['type']);
        }

        if (!empty($filter['date_from'])) {
            $supplyMovement->whereDate('created_at', '>=', $filter['date_from']);
        }

        if (!empty($filter['date_to'])) {
            $supplyMovement->whereDate('created_at', '<=', $filter['date_to']);
        }

        // Income always reflects OUT movements within the same stock/date/search
        // filters, regardless of the `type` filter above — cloned before api_list()
        // applies search + pagination to the main query.
        $incomeQuery = (clone $supplyMovement)->where('type', 'OUT');
        $search = trim((string) ($filter['search'] ?? ''));
        if ($search !== '') {
            $incomeQuery->where(function ($q) use ($search) {
                $q->where('used_for', 'like', "%{$search}%")
                    ->orWhereHas('supplyStock.supply', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }
        $totalIncome = (float) ($incomeQuery->selectRaw('COALESCE(SUM(price * quantity), 0) as total_income')->value('total_income') ?? 0);

        $result = api_list($supplyMovement, $filter, ['type', 'used_for', 'supplyStock.supply.name']);
        $result['meta']['total_income'] = $totalIncome;

        return $result;
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
