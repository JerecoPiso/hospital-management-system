<?php

namespace App\Repositories;

use App\Models\HistoryAndPhysicalExaminationFormOne;
use App\Models\PatientCase;
use Illuminate\Support\Facades\DB;

class HistoryAndPhysicalExaminationFormOneRepositories
{

    public function list($filter = [])
    {
        $history = HistoryAndPhysicalExaminationFormOne::with(['user', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $history->where('patient_case_id', $filter['patient_case_id']);
        }

        $history = $history->get();
        return $history->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $history = HistoryAndPhysicalExaminationFormOne::where('pid', $pid)->first();

            if (!$history) {
                return [];
            }

            return $history;
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

                $history = HistoryAndPhysicalExaminationFormOne::create($data);
                return $history->load(['patientCase.patient']);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($history_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $history = HistoryAndPhysicalExaminationFormOne::findOrFail($history_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            unset($data['patient_case_pid']);

            $history->update($data);

            return $history;
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
