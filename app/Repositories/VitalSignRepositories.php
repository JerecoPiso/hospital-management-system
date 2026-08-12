<?php

namespace App\Repositories;

use App\Models\PatientCase;
use App\Models\VitalSign;
use Illuminate\Support\Facades\DB;

class VitalSignRepositories
{

    public function list($filter = [])
    {
        $vitalSign = VitalSign::with(['user', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $vitalSign->where('patient_case_id', $filter['patient_case_id']);
        }


        $vitalSign = $vitalSign->get();
        return $vitalSign->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $vitalSign = VitalSign::where('pid', $pid)->first();

            if (!$vitalSign) {
                return [];
            }

            return $vitalSign;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
                unset($data['patient_case_pid']);
                $data['patient_case_id'] = $patientCase->id;

                $vitalSign = VitalSign::create($data);
                return $vitalSign->load(['patientCase.patient']);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($vital_sign_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $vitalSign = VitalSign::findOrFail($vital_sign_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            unset($data['patient_case_pid']);

            $vitalSign->update($data);

            return $vitalSign;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
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
