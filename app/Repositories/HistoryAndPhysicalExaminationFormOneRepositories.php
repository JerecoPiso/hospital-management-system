<?php

namespace App\Repositories;

use App\Models\ExaminationOption;
use App\Models\HistoryAndPhysicalExaminationFormOne;
use App\Models\PatientCase;
use Illuminate\Support\Facades\DB;

class HistoryAndPhysicalExaminationFormOneRepositories
{

    public function list($filter = [])
    {
        $history = HistoryAndPhysicalExaminationFormOne::with(['user', 'patientCase.patient', 'findings'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $history->where('patient_case_id', $filter['patient_case_id']);
        }

        $history = $history->get();
        return $history->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $history = HistoryAndPhysicalExaminationFormOne::with('findings')->where('pid', $pid)->first();

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
                $findings = $data['findings'] ?? [];
                unset($data['findings']);

                $history = HistoryAndPhysicalExaminationFormOne::create($data);
                $this->syncFindings($history, $findings);

                return $history->load(['patientCase.patient', 'findings']);
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

            return DB::transaction(function () use ($history_id, $data) {
                $history = HistoryAndPhysicalExaminationFormOne::findOrFail($history_id);

                if (!empty($data['patient_case_pid'])) {
                    $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
                }
                unset($data['patient_case_pid']);

                // Only touch findings when the request sent them
                if (array_key_exists('findings', $data)) {
                    $this->syncFindings($history, $data['findings'] ?? []);
                    unset($data['findings']);
                }

                $history->update($data);

                return $history->load('findings');
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function examinationOptions()
    {
        return ExaminationOption::where('active', true)
            ->whereIn('type', ExaminationOption::FORM_ONE_TYPES)
            ->orderBy('type', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }

    private function syncFindings(HistoryAndPhysicalExaminationFormOne $history, array $optionPids)
    {
        $optionIds = ExaminationOption::withTrashed()->whereIn('pid', $optionPids)->pluck('id')->all();
        $history->findings()->sync($optionIds);
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
