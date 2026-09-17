<?php

namespace App\Traits;

use App\Http\Requests\LabRequest\ResultsRequest;
use App\Http\Requests\LabRequest\StatusRequest;
use App\Http\Requests\LabRequest\StoreRequest;
use Illuminate\Http\Request;

trait LabRequestTrait
{
    public function list(Request $request)
    {
        try {
            $requests = $this->labRequestRepo->list($request->only(['patient_case_pid', 'status', 'priority', 'search', 'per_page', 'page']));
            return api_list_response($requests['items'], $requests['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['doctor_id'] = auth()->id();

            $labRequest = $this->labRequestRepo->store($validated);
            return api_response(["lab_request" => $labRequest], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($request_pid)
    {
        try {
            $labRequest = $this->labRequestRepo->searchByPid($request_pid);
            if (!$labRequest) {
                return api_response([], false, "Lab request not found", 404);
            }
            return api_response($labRequest, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateStatus($request_pid, StatusRequest $request)
    {
        try {
            $validated = $request->validated();
            $labRequest = $this->labRequestRepo->searchByPid($request_pid);
            if (!$labRequest) {
                return api_response([], false, "Lab request not found", 404);
            }
            $labRequest = $this->labRequestRepo->updateStatus($labRequest->id, $validated);
            return api_response(["lab_request" => $labRequest], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function saveResults($request_pid, ResultsRequest $request)
    {
        try {
            $validated = $request->validated();
            $labRequest = $this->labRequestRepo->searchByPid($request_pid);
            if (!$labRequest) {
                return api_response([], false, "Lab request not found", 404);
            }
            $labRequest = $this->labRequestRepo->saveResults($labRequest->id, $validated);
            return api_response(["lab_request" => $labRequest], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($request_pid)
    {
        try {
            $labRequest = $this->labRequestRepo->searchByPid($request_pid);
            if (!$labRequest) {
                return api_response([], false, "Lab request not found", 404);
            }
            $delete = $this->labRequestRepo->delete($labRequest);
            if (!$delete) {
                return api_response([], false, "Lab request not deleted", 500);
            }
            return api_response([], true, "Lab request deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
