<?php

namespace App\Traits;

use App\Http\Requests\PatientRegistration\StoreRequest;

trait PatientRegistrationTrait
{
    public function list()
    {
        try {
            $patients = $this->patientRegistrationRepo->list([]);
            return api_response($patients, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $patient = $this->patientRegistrationRepo->store($validated);
            return api_response(["patient" => $patient], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($patient_pid)
    {
        try {
            $patient = $this->patientRegistrationRepo->searchByPid($patient_pid);
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
            $patient = $this->patientRegistrationRepo->searchByPid($patient_pid);
            if (!$patient) {
                return api_response([], false, "Patient not found", 404);
            }
            $patient = $this->patientRegistrationRepo->update($patient->id, $validated);
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
            $patient = $this->patientRegistrationRepo->searchByPid($patient_pid);
            if (!$patient) {
                return api_response([], false, "Patient not found", 404);
            }
            $delete = $this->patientRegistrationRepo->delete($patient);
            if (!$delete) {
                return api_response([], false, "Patient not deleted", 500);
            }
            return api_response([], true, "Patient deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
