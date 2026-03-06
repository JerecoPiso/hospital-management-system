<?php

namespace App\Traits;

use App\Http\Requests\Medicine\StoreRequest;

trait MedicineTrait
{
    public function list()
    {
        try {
            $meds = $this->medicineRepo->list([]);
            return api_response($meds, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $medicine = $this->medicineRepo->store($validated);
            return api_response(["medicine" => $medicine], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($order_pid)
    {
        try {
            $medicine = $this->medicineRepo->searchByPid($order_pid);
            if (!$medicine) {
                return api_response([], false, "Medicine not found", 404);
            }
            return api_response($medicine, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($order_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $medicine = $this->medicineRepo->searchByPid($order_pid);
            if (!$medicine) {
                return api_response([], false, "Medicine not found", 404);
            }
            $medicine = $this->medicineRepo->update($medicine->id, $validated);
            if (!$medicine) {
                return api_response([], false, "Medicine not updated", 500);
            }
            return api_response(["medicine" => $medicine], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($order_pid)
    {
        try {
            $medicine = $this->medicineRepo->searchByPid($order_pid);
            if (!$medicine) {
                return api_response([], false, "Medicine not found", 404);
            }
            $delete = $this->medicineRepo->delete($medicine);
            if (!$delete) {
                return api_response([], false, "Medicine not deleted", 500);
            }
            return api_response([], true, "Medicine deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
