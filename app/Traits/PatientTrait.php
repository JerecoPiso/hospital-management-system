<?php

namespace App\Traits;

use App\Http\Requests\Patient\StoreRequest;

trait PatientTrait
{
    public function list()
    {
        try {
            $patients = $this->patientRepo->list([]);
            return api_response($patients, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $patient = $this->patientRepo->store($validated);
            return api_response(["patient" => $patient], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($patient_pid)
    {
        try {
            $patient = $this->patientRepo->searchByPid($patient_pid);
            if (!$patient) {
                return api_response([], false, "Patient not found", 404);
            }
            return api_response($patient, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($patient_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $patient = $this->patientRepo->searchByPid($patient_pid);
            if (!$patient) {
                return api_response([], false, "Patient not found", 404);
            }
            $patient = $this->patientRepo->update($patient->id, $validated);
            if (!$patient) {
                return api_response([], false, "Patient not updated", 500);
            }
            return api_response(["patient" => $patient], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($patient_pid)
    {
        try {
            $patient = $this->patientRepo->searchByPid($patient_pid);
            if (!$patient) {
                return api_response([], false, "Patient not found", 404);
            }
            $delete = $this->patientRepo->delete($patient);
            if (!$delete) {
                return api_response([], false, "Patient not deleted", 500);
            }
            return api_response([], true, "Patient deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
