<?php

namespace App\Traits;

use App\Http\Requests\Icd\StoreRequest;
use Illuminate\Http\Request;

trait IcdTrait
{
    public function list(Request $request)
    {
        try {
            $icds = $this->icdRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($icds['items'], $icds['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['status'] = $request->boolean('status', true);
            $icd = $this->icdRepo->store($validated);
            return api_response(["icd" => $icd], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($icd_pid)
    {
        try {
            $icd = $this->icdRepo->searchByPid($icd_pid);
            if (!$icd) {
                return api_response([], false, "ICD not found", 404);
            }
            return api_response($icd, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($icd_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['status'] = $request->boolean('status', true);
            $icd = $this->icdRepo->searchByPid($icd_pid);
            if (!$icd) {
                return api_response([], false, "ICD not found", 404);
            }
            $icd = $this->icdRepo->update($icd->id, $validated);
            if (!$icd) {
                return api_response([], false, "ICD not updated", 500);
            }
            return api_response(["icd" => $icd], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($icd_pid)
    {
        try {
            $icd = $this->icdRepo->searchByPid($icd_pid);
            if (!$icd) {
                return api_response([], false, "ICD not found", 404);
            }
            $delete = $this->icdRepo->delete($icd);
            if (!$delete) {
                return api_response([], false, "ICD not deleted", 500);
            }
            return api_response([], true, "ICD deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
