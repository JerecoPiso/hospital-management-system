<?php

namespace App\Repositories;

use App\Models\PatientCase;
use App\Models\Supply;
use App\Models\SupplyCharge;
use App\Models\SupplyMovement;
use App\Models\SupplyStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplyChargeRepositories
{
    public function list($filter = [])
    {
        $supplyCharge = SupplyCharge::with(['patientCase.patient', 'chargedBy', 'items.supply'])->orderBy('id', 'desc');

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
            $supplyCharge = SupplyCharge::with(['patientCase.patient', 'chargedBy', 'items.supply'])->where('pid', $pid)->first();

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

                foreach ($data['items'] as $item) {
                    $supply = Supply::where('pid', $item['supply_pid'])->firstOrFail();

                    $supplyCharge->items()->create([
                        'supply_id' => $supply->id,
                        'quantity' => $item['quantity'],
                        'remarks' => $item['remarks'] ?? null,
                    ]);

                    $this->deductFromStockFefo($supply->id, (int) round((float) $item['quantity']), $supplyCharge->pid, $patientCase->case_number);
                }

                return $supplyCharge->load(['patientCase.patient', 'chargedBy', 'items.supply']);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function deductFromStockFefo(int $supplyId, int $quantity, string $reference, ?string $caseNumber)
    {
        if ($quantity <= 0) {
            return;
        }

        $remaining = $quantity;

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

            SupplyMovement::create([
                'supply_stock_id' => $batch->id,
                'quantity' => $deduct,
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

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->items()->delete();
            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
