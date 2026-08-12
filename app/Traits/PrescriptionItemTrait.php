<?php

namespace App\Traits;

use App\Http\Requests\PrescriptionItem\UpdateRequest;

trait PrescriptionItemTrait
{
    public function view($item_pid)
    {
        try {
            $item = $this->prescriptionItemRepo->searchByPid($item_pid);
            if (!$item) {
                return api_response([], false, "Prescription item not found", 404);
            }
            return api_response($item, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($item_pid, UpdateRequest $request)
    {
        try {
            $validated = $request->validated();
            $item = $this->prescriptionItemRepo->searchByPid($item_pid);
            if (!$item) {
                return api_response([], false, "Prescription item not found", 404);
            }
            $item = $this->prescriptionItemRepo->update($item->id, $validated);
            if (!$item) {
                return api_response([], false, "Prescription item not updated", 500);
            }
            return api_response(["item" => $item], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($item_pid)
    {
        try {
            $item = $this->prescriptionItemRepo->searchByPid($item_pid);
            if (!$item) {
                return api_response([], false, "Prescription item not found", 404);
            }
            $delete = $this->prescriptionItemRepo->delete($item);
            if (!$delete) {
                return api_response([], false, "Prescription item not deleted", 500);
            }
            return api_response([], true, "Prescription item deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
