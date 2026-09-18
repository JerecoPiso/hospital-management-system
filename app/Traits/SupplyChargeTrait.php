<?php

namespace App\Traits;

use App\Http\Requests\SupplyCharge\StoreRequest;
use Illuminate\Http\Request;

trait SupplyChargeTrait
{
    public function list(Request $request)
    {
        try {
            $supplyCharges = $this->supplyChargeRepo->list($request->only(['patient_case_pid', 'search', 'per_page', 'page']));
            return api_list_response($supplyCharges['items'], $supplyCharges['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $supplyCharge = $this->supplyChargeRepo->store($validated);
            return api_response(["supply_charge" => $supplyCharge], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($supply_charge_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $supplyCharge = $this->supplyChargeRepo->searchByPid($supply_charge_pid);
            if (!$supplyCharge) {
                return api_response([], false, "Supply charge not found", 404);
            }
            $supplyCharge = $this->supplyChargeRepo->update($supplyCharge->id, $validated);
            if (!$supplyCharge) {
                return api_response([], false, "Supply charge not updated", 500);
            }
            return api_response(["supply_charge" => $supplyCharge], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($supply_charge_pid)
    {
        try {
            $supplyCharge = $this->supplyChargeRepo->searchByPid($supply_charge_pid);
            if (!$supplyCharge) {
                return api_response([], false, "Supply charge not found", 404);
            }
            return api_response($supplyCharge, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($supply_charge_pid)
    {
        try {
            $supplyCharge = $this->supplyChargeRepo->searchByPid($supply_charge_pid);
            if (!$supplyCharge) {
                return api_response([], false, "Supply charge not found", 404);
            }
            $delete = $this->supplyChargeRepo->delete($supplyCharge);
            if (!$delete) {
                return api_response([], false, "Supply charge not deleted", 500);
            }
            return api_response([], true, "Supply charge deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
