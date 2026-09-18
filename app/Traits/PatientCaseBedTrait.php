<?php
namespace App\Traits;

use Illuminate\Http\Request;
use App\Http\Requests\PatientCaseBed\StoreRequest;

trait PatientCaseBedTrait
{
     public function list(Request $request)
    {
        try {
            $filters = [];
            if ($request->has('patient_case_pid') || filled($request->input('patient_case_pid'))) {
                $case = $this->patientCaseRepo->searchByPid($request->input('patient_case_pid'));
                if ($case) {
                    $filters['patient_case_id'] = $case->id;
                }
            }
            $beds = $this->patientCaseBedRepo->list($filters);
            return api_response($beds, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["assigned_by"] = auth()->id();
            $bed = $this->patientCaseBedRepo->store($validated);
            return api_response(["bed" => $bed], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view(string $bed_pid)
    {
        try {
            $bed = $this->patientCaseBedRepo->searchByPid($bed_pid);
            if (!$bed) {
                return api_response([], false, "Patient case bed not found", 404);
            }
            return api_response($bed, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update(string $bed_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $bed = $this->patientCaseBedRepo->searchByPid($bed_pid);
            if (!$bed) {
                return api_response([], false, "Patient case bed not found", 404);
            }
            $bed = $this->patientCaseBedRepo->update($bed->id, $validated);
            if (!$bed) {
                return api_response([], false, "Patient case bed not updated", 500);
            }
            return api_response(["bed" => $bed], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete(string $bed_pid)
    {
        try {
            $bed = $this->patientCaseBedRepo->searchByPid($bed_pid);
            if (!$bed) {
                return api_response([], false, "Patient case bed not found", 404);
            }
            $delete = $this->patientCaseBedRepo->delete($bed);
            if (!$delete) {
                return api_response([], false, "Patient case bed not deleted", 500);
            }
            return api_response([], true, "Patient case bed deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
