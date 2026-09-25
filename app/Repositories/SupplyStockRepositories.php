<?php

namespace App\Repositories;

use App\Models\Supply;
use App\Models\SupplyStock;
use App\Models\SupplyMovement;
use Illuminate\Support\Facades\DB;

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

        return api_list($supplyStock, $filter, ['batch_number', 'unit_type', 'supply.name']);
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

    /**
     * The opening quantity of a new batch is logged as an IN movement so the
     * movement ledger accounts for every unit that enters stock.
     */
    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $data = $this->resolveSupplyId($data);
                $supplyStock = SupplyStock::create($data);

                $this->recordMovement($supplyStock, (int) $supplyStock->quantity, 'Stock added' . ($supplyStock->batch_number ? " (batch {$supplyStock->batch_number})" : ''));

                return $supplyStock->load('supply');
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * A manual quantity edit is logged as an IN (increase) or OUT (decrease)
     * adjustment for the difference, keeping the ledger in step with the batch.
     */
    public function update($supply_stock_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            return DB::transaction(function () use ($supply_stock_id, $data) {
                $data = $this->resolveSupplyId($data);
                $supplyStock = SupplyStock::lockForUpdate()->findOrFail($supply_stock_id);
                $previousQuantity = (int) $supplyStock->quantity;

                $supplyStock->update($data);

                $this->recordMovement($supplyStock, (int) $supplyStock->quantity - $previousQuantity, 'Stock adjusted' . ($supplyStock->batch_number ? " (batch {$supplyStock->batch_number})" : ''));

                return $supplyStock->load('supply');
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * IN movements carry the batch's purchase price. OUT adjustments are priced
     * at 0 so they don't count as charged income on the movements page.
     */
    private function recordMovement(SupplyStock $supplyStock, int $change, string $usedFor)
    {
        if ($change === 0) {
            return;
        }

        SupplyMovement::create([
            'supply_stock_id' => $supplyStock->id,
            'type' => $change > 0 ? 'IN' : 'OUT',
            'quantity' => abs($change),
            'price' => $change > 0 ? ($supplyStock->purchase_price ?? 0) : 0,
            'used_for' => $usedFor,
        ]);
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
