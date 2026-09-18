<?php

namespace App\Traits;

use App\Http\Requests\PatientCaseDischarge\StoreRequest;
use Illuminate\Http\Request;

trait PatientCaseDischargeTrait
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
            $discharges = $this->patientCaseDischargeRepo->list($filters);
            return api_response($discharges, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["discharged_by"] = auth()->id();
            $discharge = $this->patientCaseDischargeRepo->store($validated);
            return api_response(["discharge" => $discharge], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view(string $discharge_pid)
    {
        try {
            $discharge = $this->patientCaseDischargeRepo->searchByPid($discharge_pid);
            if (!$discharge) {
                return api_response([], false, "Discharge not found", 404);
            }
            return api_response($discharge, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update(string $discharge_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $discharge = $this->patientCaseDischargeRepo->searchByPid($discharge_pid);
            if (!$discharge) {
                return api_response([], false, "Discharge not found", 404);
            }
            $discharge = $this->patientCaseDischargeRepo->update($discharge->id, $validated);
            if (!$discharge) {
                return api_response([], false, "Discharge not updated", 500);
            }
            return api_response(["discharge" => $discharge], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete(string $discharge_pid)
    {
        try {
            $discharge = $this->patientCaseDischargeRepo->searchByPid($discharge_pid);
            if (!$discharge) {
                return api_response([], false, "Discharge not found", 404);
            }
            $delete = $this->patientCaseDischargeRepo->delete($discharge);
            if (!$delete) {
                return api_response([], false, "Discharge not deleted", 500);
            }
            return api_response([], true, "Discharge deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
