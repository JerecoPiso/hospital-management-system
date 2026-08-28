<?php

namespace App\Traits;

use App\Http\Requests\PertinentSignsAndSymptomsList\StoreRequest;
use Illuminate\Http\Request;

trait PertinentSignsAndSymptomsListTrait
{
    public function list(Request $request)
    {
        try {
            $items = $this->pertinentListRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($items['items'], $items['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['status'] = $request->boolean('status', true);
            $item = $this->pertinentListRepo->store($validated);
            return api_response(["pertinent_sign_and_symptom" => $item], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($pid)
    {
        try {
            $item = $this->pertinentListRepo->searchByPid($pid);
            if (!$item) {
                return api_response([], false, "Pertinent sign and symptom not found", 404);
            }
            return api_response($item, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['status'] = $request->boolean('status', true);
            $item = $this->pertinentListRepo->searchByPid($pid);
            if (!$item) {
                return api_response([], false, "Pertinent sign and symptom not found", 404);
            }
            $item = $this->pertinentListRepo->update($item->id, $validated);
            if (!$item) {
                return api_response([], false, "Pertinent sign and symptom not updated", 500);
            }
            return api_response(["pertinent_sign_and_symptom" => $item], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($pid)
    {
        try {
            $item = $this->pertinentListRepo->searchByPid($pid);
            if (!$item) {
                return api_response([], false, "Pertinent sign and symptom not found", 404);
            }
            $delete = $this->pertinentListRepo->delete($item);
            if (!$delete) {
                return api_response([], false, "Pertinent sign and symptom not deleted", 500);
            }
            return api_response([], true, "Pertinent sign and symptom deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
