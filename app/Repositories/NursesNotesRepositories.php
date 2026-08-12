<?php

namespace App\Repositories;

use App\Models\NursesNote;
use App\Models\PatientCase;
use Illuminate\Support\Facades\DB;

class NursesNotesRepositories
{

    public function list($filter = [])
    {
        $note = NursesNote::with(['nurse', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $note->where('patient_case_id', $filter['patient_case_id']);
        }

        $note = $note->get();
        return $note->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $note = NursesNote::where('pid', $pid)->first();

            if (!$note) {
                return [];
            }

            return $note;
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

                $note = NursesNote::create($data);
                return $note->load(['patientCase.patient']);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($note_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $note = NursesNote::findOrFail($note_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            unset($data['patient_case_pid']);

            $note->update($data);

            return $note;
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
