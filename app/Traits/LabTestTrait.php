<?php

namespace App\Traits;

use App\Http\Requests\LabTest\StoreRequest;
use Illuminate\Http\Request;

trait LabTestTrait
{
    public function list(Request $request)
    {
        try {
            $tests = $this->labTestRepo->list($request->only(['category_pid', 'search', 'per_page', 'page']));
            return api_list_response($tests['items'], $tests['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $test = $this->labTestRepo->store($validated);
            return api_response(["test" => $test], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($test_pid)
    {
        try {
            $test = $this->labTestRepo->searchByPid($test_pid);
            if (!$test) {
                return api_response([], false, "Lab test not found", 404);
            }
            return api_response($test, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($test_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $test = $this->labTestRepo->searchByPid($test_pid);
            if (!$test) {
                return api_response([], false, "Lab test not found", 404);
            }
            $test = $this->labTestRepo->update($test->id, $validated);
            if (!$test) {
                return api_response([], false, "Lab test not updated", 500);
            }
            return api_response(["test" => $test], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($test_pid)
    {
        try {
            $test = $this->labTestRepo->searchByPid($test_pid);
            if (!$test) {
                return api_response([], false, "Lab test not found", 404);
            }
            $delete = $this->labTestRepo->delete($test);
            if (!$delete) {
                return api_response([], false, "Lab test not deleted", 500);
            }
            return api_response([], true, "Lab test deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
