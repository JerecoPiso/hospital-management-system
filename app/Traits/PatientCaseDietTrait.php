<?php

namespace App\Traits;

use App\Http\Requests\PatientCaseDiet\ServeRequest;
use App\Http\Requests\PatientCaseDiet\StoreRequest;
use App\Http\Requests\PatientCaseDiet\UpdateRequest;
use Illuminate\Http\Request;

trait PatientCaseDietTrait
{
    public function list(Request $request)
    {
        try {
            $diets = $this->patientCaseDietRepo->list($request->only(['patient_case_pid', 'search', 'per_page', 'page']));
            return api_list_response($diets['items'], $diets['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();

            $patientCaseDiet = $this->patientCaseDietRepo->store($validated);
            return api_response(["patient_case_diet" => $patientCaseDiet], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($patient_case_diet_pid)
    {
        try {
            $patientCaseDiet = $this->patientCaseDietRepo->searchByPid($patient_case_diet_pid);
            if (!$patientCaseDiet) {
                return api_response([], false, "Patient diet not found", 404);
            }
            return api_response($patientCaseDiet, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($patient_case_diet_pid, UpdateRequest $request)
    {
        try {
            $validated = $request->validated();
            $patientCaseDiet = $this->patientCaseDietRepo->searchByPid($patient_case_diet_pid);
            if (!$patientCaseDiet) {
                return api_response([], false, "Patient diet not found", 404);
            }
            $patientCaseDiet = $this->patientCaseDietRepo->update($patientCaseDiet->id, $validated);
            if (!$patientCaseDiet) {
                return api_response([], false, "Patient diet not updated", 500);
            }
            return api_response(["patient_case_diet" => $patientCaseDiet], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($patient_case_diet_pid)
    {
        try {
            $patientCaseDiet = $this->patientCaseDietRepo->searchByPid($patient_case_diet_pid);
            if (!$patientCaseDiet) {
                return api_response([], false, "Patient diet not found", 404);
            }
            $delete = $this->patientCaseDietRepo->delete($patientCaseDiet);
            if (!$delete) {
                return api_response([], false, "Patient diet not deleted", 500);
            }
            return api_response([], true, "Patient diet deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function serve($patient_case_diet_pid, ServeRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();

            $patientCaseDiet = $this->patientCaseDietRepo->searchByPid($patient_case_diet_pid);
            if (!$patientCaseDiet) {
                return api_response([], false, "Patient diet not found", 404);
            }

            $served = $this->patientCaseDietRepo->serve($patientCaseDiet, $validated);
            return api_response(["diet_served" => $served], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
