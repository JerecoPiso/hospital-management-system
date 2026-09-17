<?php

namespace App\Traits;

use App\Http\Requests\FeeCharge\StoreRequest;
use Illuminate\Http\Request;

trait FeeChargeTrait
{
    public function list(Request $request)
    {
        try {
            $feeCharges = $this->feeChargeRepo->list($request->only(['patient_case_pid', 'search', 'per_page', 'page']));
            return api_list_response($feeCharges['items'], $feeCharges['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $feeCharge = $this->feeChargeRepo->store($validated);
            return api_response(["fee_charge" => $feeCharge], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($fee_charge_pid)
    {
        try {
            $feeCharge = $this->feeChargeRepo->searchByPid($fee_charge_pid);
            if (!$feeCharge) {
                return api_response([], false, "Fee charge not found", 404);
            }
            return api_response($feeCharge, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($fee_charge_pid)
    {
        try {
            $feeCharge = $this->feeChargeRepo->searchByPid($fee_charge_pid);
            if (!$feeCharge) {
                return api_response([], false, "Fee charge not found", 404);
            }
            $delete = $this->feeChargeRepo->delete($feeCharge);
            if (!$delete) {
                return api_response([], false, "Fee charge not deleted", 500);
            }
            return api_response([], true, "Fee charge deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
