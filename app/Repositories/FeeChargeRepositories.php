<?php

namespace App\Repositories;

use App\Models\FeeCharge;
use App\Models\FeeSchedule;
use App\Models\PatientCase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeeChargeRepositories
{
    private array $with = ['patientCase.patient', 'chargedBy', 'items.feeSchedule.feeCategory'];

    public function list($filter = [])
    {
        $query = FeeCharge::with($this->with)->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $query->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        return api_list($query, $filter, [
            'remarks',
            'chargedBy.firstname',
            'chargedBy.lastname',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
            'items.feeSchedule.name',
            'items.feeSchedule.code',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $charge = FeeCharge::with($this->with)->where('pid', $pid)->first();

            if (!$charge) {
                return [];
            }

            return $charge;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * Snapshots each fee schedule's standard_fee onto the item as unit_fee so
     * later price-list changes don't retroactively alter a patient's bill.
     */
    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();

                $charge = FeeCharge::create([
                    'patient_case_id' => $patientCase->id,
                    'charged_by' => $data['charged_by'] ?? auth()->id(),
                    'charge_date' => Carbon::parse($data['charge_date'])->format('Y-m-d H:i:s'),
                    'remarks' => $data['remarks'] ?? null,
                ]);

                foreach ($data['items'] as $item) {
                    $feeSchedule = FeeSchedule::where('pid', $item['fee_schedule_pid'])->firstOrFail();

                    $charge->items()->create([
                        'fee_schedule_id' => $feeSchedule->id,
                        'quantity' => $item['quantity'],
                        'unit_fee' => $feeSchedule->standard_fee,
                        'remarks' => $item['remarks'] ?? null,
                    ]);
                }

                return $charge->load($this->with);
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
}
