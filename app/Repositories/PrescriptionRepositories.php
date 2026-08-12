<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Models\PatientCase;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

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

        $prescription = $prescription->get();
        return $prescription->toArray();
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
                    'prescription_date' => $data['prescription_date'],
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
