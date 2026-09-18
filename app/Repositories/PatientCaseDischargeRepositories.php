<?php

namespace App\Repositories;

use App\Models\PatientCaseDischarge;
use App\Models\PatientCase;

use Illuminate\Support\Facades\DB;

class PatientCaseDischargeRepositories
{

    public function list($filter = [])
    {
        $discharge = PatientCaseDischarge::with(['dischargedBy', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $discharge->where('patient_case_id', $filter['patient_case_id']);
        }

        $discharge = $discharge->get();
        return $discharge->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $discharge = PatientCaseDischarge::with(['dischargedBy', 'patientCase.patient'])->where('pid', $pid)->first();

            if (!$discharge) {
                return [];
            }

            return $discharge;
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

        
                $patientCaseDischarge = PatientCaseDischarge::create($data);
                return $patientCaseDischarge;
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($discharge_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $discharge = PatientCaseDischarge::findOrFail($discharge_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            
            unset($data['patient_case_pid']);

            $discharge->update($data);

            return $discharge;
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
