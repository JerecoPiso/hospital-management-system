<?php

namespace App\Traits;

use App\Http\Requests\PatientCase\StoreRequest;

trait PatientCaseTrait
{
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
