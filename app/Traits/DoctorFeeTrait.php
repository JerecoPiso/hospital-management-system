<?php

namespace App\Traits;

use App\Http\Requests\DoctorFee\StoreRequest;
use Illuminate\Http\Request;

trait DoctorFeeTrait
{
    public function list(Request $request)
    {
        try {
            $doctorFees = $this->doctorFeeRepo->list($request->only(['patient_case_pid', 'search', 'per_page', 'page']));
            return api_list_response($doctorFees['items'], $doctorFees['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function doctors()
    {
        try {
            $doctors = $this->doctorFeeRepo->doctors();
            return api_response($doctors, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $doctorFee = $this->doctorFeeRepo->store($validated);
            return api_response(["doctor_fee" => $doctorFee], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($doctor_fee_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $doctorFee = $this->doctorFeeRepo->searchByPid($doctor_fee_pid);
            if (!$doctorFee) {
                return api_response([], false, "Professional fee not found", 404);
            }
            $doctorFee = $this->doctorFeeRepo->update($doctorFee->id, $validated);
            if (!$doctorFee) {
                return api_response([], false, "Professional fee not updated", 500);
            }
            return api_response(["doctor_fee" => $doctorFee], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($doctor_fee_pid)
    {
        try {
            $doctorFee = $this->doctorFeeRepo->searchByPid($doctor_fee_pid);
            if (!$doctorFee) {
                return api_response([], false, "Professional fee not found", 404);
            }
            return api_response($doctorFee, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($doctor_fee_pid)
    {
        try {
            $doctorFee = $this->doctorFeeRepo->searchByPid($doctor_fee_pid);
            if (!$doctorFee) {
                return api_response([], false, "Professional fee not found", 404);
            }
            $delete = $this->doctorFeeRepo->delete($doctorFee);
            if (!$delete) {
                return api_response([], false, "Professional fee not deleted", 500);
            }
            return api_response([], true, "Professional fee deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
