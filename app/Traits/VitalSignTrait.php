<?php

namespace App\Traits;

use App\Http\Requests\VitalSign\StoreRequest;

trait VitalSignTrait
{
    public function list()
    {
        try {
            $vitalSigns = $this->vitalSignRepo->list([]);
            return api_response($vitalSigns, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = auth()->id();
            $vitalSign = $this->vitalSignRepo->store($validated);
            return api_response(["vital_sign" => $vitalSign], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($vital_sign_pid)
    {
        try {
            $vitalSign = $this->vitalSignRepo->searchByPid($vital_sign_pid);
            if (!$vitalSign) {
                return api_response([], false, "Vital sign not found", 404);
            }
            return api_response($vitalSign, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($vital_sign_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $vitalSign = $this->vitalSignRepo->searchByPid($vital_sign_pid);
            if (!$vitalSign) {
                return api_response([], false, "Vital sign not found", 404);
            }
            $vitalSign = $this->vitalSignRepo->update($vitalSign->id, $validated);
            if (!$vitalSign) {
                return api_response([], false, "Vital sign not updated", 500);
            }
            return api_response(["vital_sign" => $vitalSign], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($vital_sign_pid)
    {
        try {
            $vitalSign = $this->vitalSignRepo->searchByPid($vital_sign_pid);
            if (!$vitalSign) {
                return api_response([], false, "Vital sign not found", 404);
            }
            $delete = $this->vitalSignRepo->delete($vitalSign);
            if (!$delete) {
                return api_response([], false, "Vital sign not deleted", 500);
            }
            return api_response([], true, "Vital sign deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
