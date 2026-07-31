<?php

namespace App\Traits;

use App\Http\Requests\Supply\StoreRequest;

trait SupplyTrait
{
    public function list()
    {
        try {
            $supplies = $this->supplyRepo->list([]);
            return api_response($supplies, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $supply = $this->supplyRepo->store($validated);
            return api_response(["supply" => $supply], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($supply_pid)
    {
        try {
            $supply = $this->supplyRepo->searchByPid($supply_pid);
            if (!$supply) {
                return api_response([], false, "Supply not found", 404);
            }
            return api_response($supply, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($supply_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $supply = $this->supplyRepo->searchByPid($supply_pid);
            if (!$supply) {
                return api_response([], false, "Supply not found", 404);
            }
            $supply = $this->supplyRepo->update($supply->id, $validated);
            if (!$supply) {
                return api_response([], false, "Supply not updated", 500);
            }
            return api_response(["supply" => $supply], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($supply_pid)
    {
        try {
            $supply = $this->supplyRepo->searchByPid($supply_pid);
            if (!$supply) {
                return api_response([], false, "Supply not found", 404);
            }
            $delete = $this->supplyRepo->delete($supply);
            if (!$delete) {
                return api_response([], false, "Supply not deleted", 500);
            }
            return api_response([], true, "Supply deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
