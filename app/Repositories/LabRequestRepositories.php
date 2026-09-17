<?php

namespace App\Repositories;

use App\Models\LabRequest;
use App\Models\LabTest;
use App\Models\LabTestParameter;
use App\Models\PatientCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabRequestRepositories
{
    private array $with = ['patientCase.patient', 'doctor', 'labTest.category', 'labTest.parameters', 'results.parameter', 'results.enteredBy', 'results.verifiedBy'];

    public function list($filter = [])
    {
        $query = LabRequest::with($this->with)->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $query->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (!empty($filter['priority'])) {
            $query->where('priority', $filter['priority']);
        }

        return api_list($query, $filter, [
            'request_number',
            'status',
            'priority',
            'labTest.name',
            'labTest.code',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $request = LabRequest::with($this->with)->where('pid', $pid)->first();

            if (!$request) {
                return [];
            }

            return $request;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
            $labTest = LabTest::where('pid', $data['lab_test_pid'])->firstOrFail();

            $request = LabRequest::create([
                'request_number' => 'LR-' . strtoupper(Str::random(8)),
                'patient_case_id' => $patientCase->id,
                'doctor_id' => $data['doctor_id'] ?? null,
                'lab_test_id' => $labTest->id,
                'price' => $labTest->price,
                'status' => 'pending',
                'priority' => $data['priority'] ?? 'routine',
                'clinical_notes' => $data['clinical_notes'] ?? null,
            ]);

            return $request->load($this->with);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function updateStatus($request_id, $data)
    {
        try {
            $request = LabRequest::findOrFail($request_id);
            $request->update(['status' => $data['status']]);

            return $request->load($this->with);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * Upserts one result row per requested parameter. Re-submitting a
     * parameter overwrites its previous value rather than duplicating it.
     */
    public function saveResults($request_id, $data)
    {
        try {
            return DB::transaction(function () use ($request_id, $data) {
                $request = LabRequest::findOrFail($request_id);

                foreach ($data['results'] as $result) {
                    $parameter = LabTestParameter::where('pid', $result['parameter_pid'])->firstOrFail();

                    $request->results()->updateOrCreate(
                        ['parameter_id' => $parameter->id],
                        [
                            'result_value' => $result['result_value'],
                            'is_abnormal' => $result['is_abnormal'] ?? false,
                            'entered_by' => auth()->id(),
                        ]
                    );
                }

                return $request->load($this->with);
            });
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

            $data->results()->delete();
            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
