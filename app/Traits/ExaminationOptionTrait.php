<?php

namespace App\Traits;

use App\Http\Requests\ExaminationOption\StoreRequest;
use Illuminate\Http\Request;

trait ExaminationOptionTrait
{
    public function list(Request $request)
    {
        try {
            $items = $this->examinationOptionRepo->list($request->only(['search', 'per_page', 'page', 'type', 'active']));
            return api_list_response($items['items'], $items['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['active'] = $request->boolean('active', true);
            $item = $this->examinationOptionRepo->store($validated);
            return api_response(["examination_option" => $item], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($pid)
    {
        try {
            $item = $this->examinationOptionRepo->searchByPid($pid);
            if (!$item) {
                return api_response([], false, "Examination option not found", 404);
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
            $validated['active'] = $request->boolean('active', true);
            $item = $this->examinationOptionRepo->searchByPid($pid);
            if (!$item) {
                return api_response([], false, "Examination option not found", 404);
            }
            $item = $this->examinationOptionRepo->update($item->id, $validated);
            if (!$item) {
                return api_response([], false, "Examination option not updated", 500);
            }
            return api_response(["examination_option" => $item], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($pid)
    {
        try {
            $item = $this->examinationOptionRepo->searchByPid($pid);
            if (!$item) {
                return api_response([], false, "Examination option not found", 404);
            }
            $delete = $this->examinationOptionRepo->delete($item);
            if (!$delete) {
                return api_response([], false, "Examination option not deleted", 500);
            }
            return api_response([], true, "Examination option deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
