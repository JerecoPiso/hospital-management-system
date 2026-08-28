<?php

namespace App\Traits;

use App\Http\Requests\PertinentSignsAndSymptoms\StoreRequest;
use Illuminate\Http\Request;

trait PertinentSignsAndSymptomsTrait
{
    public function list(Request $request)
    {
        try {
            $filters = [];
            if (filled($request->input('patient_case_pid'))) {
                $case = $this->patientCaseRepo->searchByPid($request->input('patient_case_pid'));
                if ($case) {
                    $filters['patient_case_id'] = $case->id;
                }
            }
            $records = $this->pertinentRepo->list($filters);
            return api_response($records, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["doctor_id"] = auth()->id();
            $record = $this->pertinentRepo->store($validated);
            return api_response(["pertinent_signs_and_symptoms" => $record], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($pid)
    {
        try {
            $record = $this->pertinentRepo->searchByPid($pid);
            if (!$record) {
                return api_response([], false, "Pertinent signs and symptoms not found", 404);
            }
            return api_response($record, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $record = $this->pertinentRepo->searchByPid($pid);
            if (!$record) {
                return api_response([], false, "Pertinent signs and symptoms not found", 404);
            }
            $record = $this->pertinentRepo->update($record->id, $validated);
            if (!$record) {
                return api_response([], false, "Pertinent signs and symptoms not updated", 500);
            }
            return api_response(["pertinent_signs_and_symptoms" => $record], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($pid)
    {
        try {
            $record = $this->pertinentRepo->searchByPid($pid);
            if (!$record) {
                return api_response([], false, "Pertinent signs and symptoms not found", 404);
            }
            $delete = $this->pertinentRepo->delete($record);
            if (!$delete) {
                return api_response([], false, "Pertinent signs and symptoms not deleted", 500);
            }
            return api_response([], true, "Pertinent signs and symptoms deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
