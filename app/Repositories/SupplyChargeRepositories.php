<?php

namespace App\Repositories;

use App\Models\PatientCase;
use App\Models\Supply;
use App\Models\SupplyCharge;
use App\Models\SupplyChargeItem;
use App\Models\SupplyMovement;
use App\Models\SupplyStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplyChargeRepositories
{
    private array $with = ['patientCase.patient', 'chargedBy', 'items.supply'];

    public function list($filter = [])
    {
        $supplyCharge = SupplyCharge::with($this->with)->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $supplyCharge->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        return api_list($supplyCharge, $filter, [
            'remarks',
            'chargedBy.firstname',
            'chargedBy.lastname',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
            'items.supply.name',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $supplyCharge = SupplyCharge::with($this->with)->where('pid', $pid)->first();

            if (!$supplyCharge) {
                return [];
            }

            return $supplyCharge;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * Charging a supply to a patient deducts stock immediately, oldest-expiring
     * batch first (FEFO) — the same mechanism used for prescription dispensing.
     */
    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();

                $supplyCharge = SupplyCharge::create([
                    'patient_case_id' => $patientCase->id,
                    'charged_by' => $data['charged_by'] ?? auth()->id(),
                    'charge_date' => Carbon::parse($data['charge_date'])->format('Y-m-d H:i:s'),
                    'remarks' => $data['remarks'] ?? null,
                ]);

                $this->syncItems($supplyCharge, $data['items'], $patientCase->case_number);

                return $supplyCharge->load($this->with);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Items are replaced wholesale rather than diffed: stock deducted for the
     * old items is returned first, then the new item set is charged and
     * deducted from stock the same way store() does.
     */
    public function update($supply_charge_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            return DB::transaction(function () use ($supply_charge_id, $data) {
                $supplyCharge = SupplyCharge::with('items')->findOrFail($supply_charge_id);

                $update = [
                    'charge_date' => Carbon::parse($data['charge_date'])->format('Y-m-d H:i:s'),
                    'remarks' => $data['remarks'] ?? null,
                ];

                if (!empty($data['patient_case_pid'])) {
                    $update['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
                }

                $supplyCharge->update($update);

                if (isset($data['items'])) {
                    foreach ($supplyCharge->items as $item) {
                        $this->restoreStockForItem($item);
                    }
                    $supplyCharge->items()->delete();

                    $patientCase = PatientCase::findOrFail($supplyCharge->patient_case_id);
                    $this->syncItems($supplyCharge, $data['items'], $patientCase->case_number);
                }

                return $supplyCharge->load($this->with);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function syncItems(SupplyCharge $supplyCharge, array $items, ?string $caseNumber)
    {
        foreach ($items as $item) {
            $supply = Supply::where('pid', $item['supply_pid'])->firstOrFail();

            $chargeItem = $supplyCharge->items()->create([
                'supply_id' => $supply->id,
                'price' => $supply->selling_price,
                'quantity' => $item['quantity'],
                'remarks' => $item['remarks'] ?? null,
            ]);

            $this->deductFromStockFefo($supply->id, (int) round((float) $item['quantity']), $chargeItem->id, $caseNumber, $supply->selling_price);
        }
    }

    private function deductFromStockFefo(int $supplyId, int $quantity, int $supplyChargeItemId, ?string $caseNumber, float $selling_price = 0)
    {
        if ($quantity <= 0) {
            return;
        }

        $remaining = $quantity;

        $batches = SupplyStock::with(['supply'])->where('supply_id', $supplyId)
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

            SupplyMovement::create([
                'supply_stock_id' => $batch->id,
                'supply_charge_item_id' => $supplyChargeItemId,
                'quantity' => $deduct,
                // 'price' => $batch->supply->selling_price,
                'price' => $selling_price,
                'type' => 'OUT',
                'used_for' => 'Charged to patient' . ($caseNumber ? " (case {$caseNumber})" : '') . ($batch->batch_number ? " — batch {$batch->batch_number}" : ''),
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            $supplyName = Supply::find($supplyId)?->name ?? "supply #{$supplyId}";
            throw new \Exception("Insufficient stock for {$supplyName}: short by {$remaining} unit(s).");
        }
    }

    /**
     * Reverses exactly the batches a charge item's deduction drew from, using
     * the OUT movements recorded for it, and logs an offsetting IN movement
     * per batch for the audit trail.
     */
    private function restoreStockForItem(SupplyChargeItem $item)
    {
        $movements = SupplyMovement::where('supply_charge_item_id', $item->id)
            ->where('type', 'OUT')
            ->lockForUpdate()
            ->get();

        foreach ($movements as $movement) {
            $batch = SupplyStock::lockForUpdate()->find($movement->supply_stock_id);

            if (!$batch) {
                continue;
            }

            $batch->quantity += $movement->quantity;
            $batch->save();

            SupplyMovement::create([
                'supply_stock_id' => $batch->id,
                'supply_charge_item_id' => $item->id,
                'quantity' => $movement->quantity,
                'price' => $movement->price,
                'type' => 'IN',
                'used_for' => 'Returned to stock — supply charge item removed' . ($batch->batch_number ? " (batch {$batch->batch_number})" : ''),
            ]);
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            return DB::transaction(function () use ($data) {
                foreach ($data->items as $item) {
                    $this->restoreStockForItem($item);
                }

                $data->items()->delete();
                $data->delete();

                return true;
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
