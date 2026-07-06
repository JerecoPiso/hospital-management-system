<?php

namespace App\Traits;

use App\Http\Requests\HistoryAndPhysicalExaminationFormTwo\StoreRequest;

trait HistoryAndPhysicalExaminationFormTwoTrait
{
    public function list()
    {
        try {
            $histories = $this->historyFormTwoRepo->list([]);
            return api_response($histories, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = auth()->id();
            $history = $this->historyFormTwoRepo->store($validated);
            return api_response(["history" => $history], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($history_pid)
    {
        try {
            $history = $this->historyFormTwoRepo->searchByPid($history_pid);
            if (!$history) {
                return api_response([], false, "History and Physical Examination Form not found", 404);
            }
            return api_response($history, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($history_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $history = $this->historyFormTwoRepo->searchByPid($history_pid);
            if (!$history) {
                return api_response([], false, "History and Physical Examination Form not found", 404);
            }
            $history = $this->historyFormTwoRepo->update($history->id, $validated);
            if (!$history) {
                return api_response([], false, "History and Physical Examination Form not updated", 500);
            }
            return api_response(["history" => $history], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($history_pid)
    {
        try {
            $history = $this->historyFormTwoRepo->searchByPid($history_pid);
            if (!$history) {
                return api_response([], false, "History and Physical Examination Form not found", 404);
            }
            $delete = $this->historyFormTwoRepo->delete($history);
            if (!$delete) {
                return api_response([], false, "History and Physical Examination Form not deleted", 500);
            }
            return api_response([], true, "History and Physical Examination Form deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
