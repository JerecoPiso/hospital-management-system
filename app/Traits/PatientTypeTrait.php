<?php

namespace App\Traits;

use App\Http\Requests\PatientType\StoreRequest;
use Illuminate\Http\Request;

trait PatientTypeTrait
{
    public function list(Request $request)
    {
        try {
            $patientTypes = $this->patientTypeRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($patientTypes['items'], $patientTypes['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $patientType = $this->patientTypeRepo->store($validated);
            return api_response(["patient_type" => $patientType], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($patient_type_pid)
    {
        try {
            $patientType = $this->patientTypeRepo->searchByPid($patient_type_pid);
            if (!$patientType) {
                return api_response([], false, "Patient type not found", 404);
            }
            return api_response($patientType, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($patient_type_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $patientType = $this->patientTypeRepo->searchByPid($patient_type_pid);
            if (!$patientType) {
                return api_response([], false, "Patient type not found", 404);
            }
            $patientType = $this->patientTypeRepo->update($patientType->id, $validated);
            if (!$patientType) {
                return api_response([], false, "Patient type not updated", 500);
            }
            return api_response(["patient_type" => $patientType], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($patient_type_pid)
    {
        try {
            $patientType = $this->patientTypeRepo->searchByPid($patient_type_pid);
            if (!$patientType) {
                return api_response([], false, "Patient type not found", 404);
            }
            $delete = $this->patientTypeRepo->delete($patientType);
            if (!$delete) {
                return api_response([], false, "Patient type not deleted", 500);
            }
            return api_response([], true, "Patient type deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
