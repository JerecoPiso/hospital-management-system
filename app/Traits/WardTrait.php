<?php

namespace App\Traits;

use App\Http\Requests\Ward\StoreRequest;
use Illuminate\Http\Request;

trait WardTrait
{
    public function list(Request $request)
    {
        try {
            $wards = $this->wardRepo->list($request->only(['floor_pid']));
            return api_response($wards, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $ward = $this->wardRepo->store($validated);
            return api_response(["ward" => $ward], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($ward_pid)
    {
        try {
            $ward = $this->wardRepo->searchByPid($ward_pid);
            if (!$ward) {
                return api_response([], false, "Ward not found", 404);
            }
            return api_response($ward, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($ward_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $ward = $this->wardRepo->searchByPid($ward_pid);
            if (!$ward) {
                return api_response([], false, "Ward not found", 404);
            }
            $ward = $this->wardRepo->update($ward->id, $validated);
            if (!$ward) {
                return api_response([], false, "Ward not updated", 500);
            }
            return api_response(["ward" => $ward], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($ward_pid)
    {
        try {
            $ward = $this->wardRepo->searchByPid($ward_pid);
            if (!$ward) {
                return api_response([], false, "Ward not found", 404);
            }
            $delete = $this->wardRepo->delete($ward);
            if (!$delete) {
                return api_response([], false, "Ward not deleted", 500);
            }
            return api_response([], true, "Ward deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
