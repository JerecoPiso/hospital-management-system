<?php

namespace App\Repositories;

use App\Models\PatientType;

class PatientTypeRepositories
{
    public function list($filter = [])
    {
        $query = PatientType::orderBy('id', 'desc');

        return api_list($query, $filter, ['code', 'name', 'description']);
    }

    public function searchByPid($pid)
    {
        try {
            $patientType = PatientType::where('pid', $pid)->first();

            if (!$patientType) {
                return [];
            }

            return $patientType;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $patientType = PatientType::create($data);
            return $patientType;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($patient_type_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $patientType = PatientType::findOrFail($patient_type_id);
            $patientType->update($data);

            return $patientType;
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
