<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\MedicineStockMovement;
use App\Models\PatientCase;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrescriptionRepositories
{
    public function list($filter = [])
    {
        $prescription = Prescription::with(['patientCase.patient', 'doctor', 'items.medicine'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $prescription->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        if (!empty($filter['status'])) {
            $prescription->where('status', $filter['status']);
        }

        return api_list($prescription, $filter, [
            'status',
            'remarks',
            'doctor.firstname',
            'doctor.lastname',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
            'items.medicine.name',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $prescription = Prescription::with(['patientCase.patient', 'doctor', 'items.medicine'])->where('pid', $pid)->first();

            if (!$prescription) {
                return [];
            }

            return $prescription;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();

                $prescription = Prescription::create([
                    'patient_case_id' => $patientCase->id,
                    'doctor_id' => $data['doctor_id'],
                    'prescription_date' => $data['prescription_date'],
                    'remarks' => $data['remarks'] ?? null,
                    'status' => $data['status'] ?? 'requested',
                ]);

                $this->syncItems($prescription, $data['items']);

                return $prescription->load(['patientCase.patient', 'doctor', 'items.medicine']);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update($prescription_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            return DB::transaction(function () use ($prescription_id, $data) {
                $prescription = Prescription::findOrFail($prescription_id);

                $update = [
                    'prescription_date' => !empty($data['prescription_date'])
                        ? Carbon::parse($data['prescription_date'])->format('Y-m-d H:i:s')
                        : null,
                    'remarks' => $data['remarks'] ?? null,
                    'status' => $data['status'] ?? $prescription->status,
                ];

                if (!empty($data['patient_case_pid'])) {
                    $update['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
                }

                $prescription->update($update);

                if (isset($data['items'])) {
                    $prescription->items()->delete();
                    $this->syncItems($prescription, $data['items']);
                }

                return $prescription->load(['patientCase.patient', 'doctor', 'items.medicine']);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateStatus($prescription_id, $data)
    {
        try {
            return DB::transaction(function () use ($prescription_id, $data) {
                $prescription = Prescription::with('items')->lockForUpdate()->findOrFail($prescription_id);
                $wasDone = $prescription->status === 'done';

                $prescription->update([
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? $prescription->remarks,
                ]);

                if ($data['status'] === 'done' && !$wasDone) {
                    $this->dispenseInventory($prescription);
                }

                return $prescription->load(['patientCase.patient', 'doctor', 'items.medicine']);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * Deduct dispensed quantities from medicine stock, oldest-expiring batch first (FEFO).
     * If a batch doesn't have enough on hand, the remainder rolls over to the next
     * soonest-to-expire batch until the prescribed quantity is fully covered.
     */
    private function dispenseInventory(Prescription $prescription)
    {
        foreach ($prescription->items as $item) {
            $quantity = (int) round((float) ($item->quantity ?? 0));

            if ($quantity <= 0) {
                continue;
            }

            $this->deductFromStockFefo($item->medicine_id, $quantity, $prescription->pid);
        }
    }

    private function deductFromStockFefo(int $medicineId, int $quantity, string $reference)
    {
        $remaining = $quantity;

        $batches = MedicineStock::with(['medicine'])->where('medicine_id', $medicineId)
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

            MedicineStockMovement::create([
                'medicine_stock_id' => $batch->id,
                'price' => $batch->medicine->price,
                'type' => 'OUT',
                'quantity' => $deduct,
                'reference' => $reference,
                'remarks' => 'Dispensed for prescription' . ($batch->batch_number ? " (batch {$batch->batch_number})" : ''),
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            $medicineName = Medicine::find($medicineId)?->name ?? "medicine #{$medicineId}";
            throw new \Exception("Insufficient stock for {$medicineName}: short by {$remaining} unit(s).");
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

    private function syncItems(Prescription $prescription, array $items)
    {
        foreach ($items as $item) {
            $medicine = Medicine::where('pid', $item['medicine_pid'])->firstOrFail();

            $prescription->items()->create([
                'medicine_id' => $medicine->id,
                'frequency' => $item['frequency'] ?? null,
                'duration' => $item['duration'] ?? null,
                'duration_unit' => $item['duration_unit'] ?? null,
                'quantity' => $item['quantity'] ?? null,
                'instructions' => $item['instructions'] ?? null,
                'remarks' => $item['remarks'] ?? null,
                'status' => $item['status'] ?? 'continue',
            ]);
        }
    }
}
