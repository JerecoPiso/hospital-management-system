<?php

namespace App\Traits;

use App\Http\Requests\RadiologyModality\StoreRequest;
use Illuminate\Http\Request;

trait RadiologyModalityTrait
{
    public function list(Request $request)
    {
        try {
            $modalities = $this->radiologyModalityRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($modalities['items'], $modalities['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $modality = $this->radiologyModalityRepo->store($validated);
            return api_response(["modality" => $modality], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($modality_pid)
    {
        try {
            $modality = $this->radiologyModalityRepo->searchByPid($modality_pid);
            if (!$modality) {
                return api_response([], false, "Radiology modality not found", 404);
            }
            return api_response($modality, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($modality_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $modality = $this->radiologyModalityRepo->searchByPid($modality_pid);
            if (!$modality) {
                return api_response([], false, "Radiology modality not found", 404);
            }
            $modality = $this->radiologyModalityRepo->update($modality->id, $validated);
            if (!$modality) {
                return api_response([], false, "Radiology modality not updated", 500);
            }
            return api_response(["modality" => $modality], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($modality_pid)
    {
        try {
            $modality = $this->radiologyModalityRepo->searchByPid($modality_pid);
            if (!$modality) {
                return api_response([], false, "Radiology modality not found", 404);
            }
            $delete = $this->radiologyModalityRepo->delete($modality);
            if (!$delete) {
                return api_response([], false, "Radiology modality not deleted", 500);
            }
            return api_response([], true, "Radiology modality deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
