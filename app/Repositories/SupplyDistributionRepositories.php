<?php

namespace App\Repositories;

use App\Models\Station;
use App\Models\Supply;
use App\Models\SupplyDistribution;
use App\Models\SupplyMovement;
use App\Models\SupplyStock;
use Illuminate\Database\Eloquent\Collection;
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
                $supply = Supply::where('pid', $data['supply_pid'])->firstOrFail();
                $station = Station::where('pid', $data['station_pid'])->firstOrFail();

                $distributedBy = $data['distributed_by'] ?? auth()->id();
                $distributedAt = $data['distributed_at'] ?? now();

                $distributions = $this->distributeFefo($supply->id, (int) $data['quantity'], $station->id, $distributedBy, $distributedAt);

                return $distributions->load(['supplyStock.supply', 'station', 'distributedBy']);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Distribute the requested quantity to a station, drawing from the
     * soonest-to-expire batch first (FEFO). If a batch can't cover the full
     * amount, the remainder rolls over to the next soonest-to-expire batch
     * until the requested quantity is fully covered — one distribution (and
     * movement) record is created per batch actually drawn from.
     */
    private function distributeFefo(int $supplyId, int $quantity, int $stationId, $distributedBy, $distributedAt)
    {
        $remaining = $quantity;
        $created = new Collection();

        $batches = SupplyStock::where('supply_id', $supplyId)
            ->where('quantity', '>', 0)
            ->orderByRaw('expiration_date IS NULL, expiration_date ASC')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $deduct = min($batch->quantity, $remaining);
            $batch->quantity -= $deduct;
            $batch->save();

            $created->push(SupplyDistribution::create([
                'supply_stock_id' => $batch->id,
                'station_id' => $stationId,
                'quantity' => $deduct,
                'distributed_by' => $distributedBy,
                'distributed_at' => $distributedAt,
            ]));

            SupplyMovement::create([
                'supply_stock_id' => $batch->id,
                'quantity' => $deduct,
                'type' => 'OUT',
                'used_for' => 'Distributed to station' . ($batch->batch_number ? " (batch {$batch->batch_number})" : ''),
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            $supplyName = Supply::find($supplyId)?->name ?? "supply #{$supplyId}";
            throw new \Exception("Insufficient stock for {$supplyName}: short by {$remaining} unit(s).");
        }

        return $created;
    }
}
