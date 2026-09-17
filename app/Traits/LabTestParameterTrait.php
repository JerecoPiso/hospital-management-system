<?php

namespace App\Traits;

use App\Http\Requests\LabTestParameter\StoreRequest;
use Illuminate\Http\Request;

trait LabTestParameterTrait
{
    public function list(Request $request)
    {
        try {
            $parameters = $this->labTestParameterRepo->list($request->only(['lab_test_pid', 'search', 'per_page', 'page']));
            return api_list_response($parameters['items'], $parameters['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $parameter = $this->labTestParameterRepo->store($validated);
            return api_response(["parameter" => $parameter], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($parameter_pid)
    {
        try {
            $parameter = $this->labTestParameterRepo->searchByPid($parameter_pid);
            if (!$parameter) {
                return api_response([], false, "Lab test parameter not found", 404);
            }
            return api_response($parameter, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($parameter_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $parameter = $this->labTestParameterRepo->searchByPid($parameter_pid);
            if (!$parameter) {
                return api_response([], false, "Lab test parameter not found", 404);
            }
            $parameter = $this->labTestParameterRepo->update($parameter->id, $validated);
            if (!$parameter) {
                return api_response([], false, "Lab test parameter not updated", 500);
            }
            return api_response(["parameter" => $parameter], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($parameter_pid)
    {
        try {
            $parameter = $this->labTestParameterRepo->searchByPid($parameter_pid);
            if (!$parameter) {
                return api_response([], false, "Lab test parameter not found", 404);
            }
            $delete = $this->labTestParameterRepo->delete($parameter);
            if (!$delete) {
                return api_response([], false, "Lab test parameter not deleted", 500);
            }
            return api_response([], true, "Lab test parameter deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
