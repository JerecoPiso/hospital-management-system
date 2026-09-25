<?php

namespace App\Repositories;

use App\Models\DoctorFee;
use App\Models\PatientCase;
use App\Models\User;

class DoctorFeeRepositories
{
    private array $with = ['patientCase.patient', 'doctor', 'addedBy', 'invoiceItem'];

    public function list($filter = [])
    {
        $query = DoctorFee::with($this->with)->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $query->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        return api_list($query, $filter, [
            'doctor.firstname',
            'doctor.lastname',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
        ]);
    }

    /**
     * Roles are user-defined, so a "doctor" is anyone whose role name
     * mentions doctor/physician, or who has a PRC license number on file.
     */
    public function doctors()
    {
        return User::with('role')
            ->where(function ($q) {
                $q->whereHas('role', function ($r) {
                    $r->where('name', 'like', '%doctor%')->orWhere('name', 'like', '%physician%');
                })->orWhere(function ($l) {
                    $l->whereNotNull('license_no')->where('license_no', '!=', '');
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();
    }

    public function searchByPid($pid)
    {
        try {
            $fee = DoctorFee::with($this->with)->where('pid', $pid)->first();

            if (!$fee) {
                return [];
            }

            return $fee;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
            $doctor = User::where('pid', $data['doctor_pid'])->firstOrFail();

            $fee = DoctorFee::create([
                'patient_case_id' => $patientCase->id,
                'doctor_id' => $doctor->id,
                'added_by' => auth()->id(),
                'professional_fee' => $data['professional_fee'],
            ]);

            return $fee->load($this->with);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Once a professional fee has been pulled into an invoice it is locked,
     * otherwise the invoice total would no longer match its source charge.
     */
    public function update($fee_id, $data)
    {
        if (!$data) {
            return null;
        }

        $fee = DoctorFee::with('invoiceItem')->findOrFail($fee_id);
        if ($fee->invoiceItem) {
            throw new \Exception("Professional fee is already invoiced and can no longer be modified.", 422);
        }

        try {
            $doctor = User::where('pid', $data['doctor_pid'])->firstOrFail();

            $fee->update([
                'doctor_id' => $doctor->id,
                'professional_fee' => $data['professional_fee'],
            ]);

            return $fee->load($this->with);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($data)
    {
        if (!$data) {
            return;
        }

        if ($data->invoiceItem) {
            throw new \Exception("Professional fee is already invoiced and can no longer be deleted.", 422);
        }

        try {
            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
