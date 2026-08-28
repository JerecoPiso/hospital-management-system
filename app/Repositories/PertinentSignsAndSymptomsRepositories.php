<?php

namespace App\Repositories;

use App\Models\PatientCase;
use App\Models\PertinentSignsAndSymptom;
use Illuminate\Support\Facades\DB;

class PertinentSignsAndSymptomsRepositories
{
    public function list($filter = [])
    {
        $query = PertinentSignsAndSymptom::with(['user', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $query->where('patient_case_id', $filter['patient_case_id']);
        }

        return $query->get()->toArray();
    }

    public function searchByPid($pid)
    {
        try {
            $record = PertinentSignsAndSymptom::where('pid', $pid)->first();

            if (!$record) {
                return [];
            }

            return $record;
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
                $data['values'] = $data['values'] ?? '';

                $record = PertinentSignsAndSymptom::create($data);
                return $record->load(['user', 'patientCase.patient']);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $record = PertinentSignsAndSymptom::findOrFail($id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            unset($data['patient_case_pid']);
            $data['values'] = $data['values'] ?? '';

            $record->update($data);

            return $record->load(['user', 'patientCase.patient']);
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
