<?php

namespace App\Traits;

use App\Http\Requests\PatientCase\StoreRequest;

trait PatientCaseTrait
{
    public function view($patient_case_pid)
    {
        try {
            $patientCase = $this->patientCaseRepo->searchByPid($patient_case_pid);
            if (!$patientCase) {
                return api_response([], false, "Patient case not found", 404);
            }
            return api_response($patientCase, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $patientCase = $this->patientCaseRepo->store($validated);
            return api_response(["patient_case" => $patientCase], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
