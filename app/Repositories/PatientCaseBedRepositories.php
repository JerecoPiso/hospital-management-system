<?php

namespace App\Repositories;

use App\Models\PatientCaseBed;
use App\Models\PatientCase;
use App\Models\Bed;

use Illuminate\Support\Facades\DB;

class PatientCaseBedRepositories
{

    public function list($filter = [])
    {
        $bed = PatientCaseBed::with(['bed.room.ward', 'assignedBy', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $bed->where('patient_case_id', $filter['patient_case_id']);
        }

        $bed = $bed->get();
        return $bed->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $bed = PatientCaseBed::with(['bed.room.ward', 'assignedBy', 'patientCase.patient'])->where('pid', $pid)->first();

            if (!$bed) {
                return [];
            }

            return $bed;
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

                $bed = Bed::where('pid', $data['bed_pid'])->firstOrFail();
                unset($data['bed_pid']);
                $data['bed_id'] = $bed->id;
                $data['price'] = $bed->price ?? 0;
                $patientCaseBed = PatientCaseBed::create($data);
                return $patientCaseBed;
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($bed_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $bed = PatientCaseBed::findOrFail($bed_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            if (!empty($data['bed_pid'])) {
                $bedToUse = Bed::where('pid', $data['bed_pid'])->firstOrFail();
                $data['bed_id'] = $bedToUse->id;
                $data['price'] = $bedToUse->price;
            }
            unset($data['patient_case_pid']);
            unset($data['bed_pid']);

            $bed->update($data);

            return $bed;
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
