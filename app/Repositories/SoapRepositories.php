<?php

namespace App\Repositories;

use App\Models\Icd;
use App\Models\PatientCase;
use App\Models\Soap;

class SoapRepositories
{
    public function list($filter = [])
    {
        $soap = Soap::with(['doctor', 'icd', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $soap->where('patient_case_id', $filter['patient_case_id']);
        }

        $soap = $soap->get();
        return $soap->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $soap = Soap::with(['doctor', 'icd', 'patientCase.patient'])->where('pid', $pid)->first();

            if (!$soap) {
                return [];
            }

            return $soap;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
            $icd = Icd::where('pid', $data['icd_pid'])->firstOrFail();

            unset($data['patient_case_pid'], $data['icd_pid']);
            $data['patient_case_id'] = $patientCase->id;
            $data['icd_id'] = $icd->id;

            $soap = Soap::create($data);
            return $soap->load(['doctor', 'icd', 'patientCase.patient']);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($soap_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $soap = Soap::findOrFail($soap_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            if (!empty($data['icd_pid'])) {
                $data['icd_id'] = Icd::where('pid', $data['icd_pid'])->firstOrFail()->id;
            }
            unset($data['patient_case_pid'], $data['icd_pid']);

            $soap->update($data);

            return $soap->load(['doctor', 'icd', 'patientCase.patient']);
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
