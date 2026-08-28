<?php

namespace App\Traits;

use App\Http\Requests\Diet\StoreRequest;
use Illuminate\Http\Request;

trait DietTrait
{
    public function list(Request $request)
    {
        try {
            $diets = $this->dietRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($diets['items'], $diets['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $diet = $this->dietRepo->store($validated);
            return api_response(["diet" => $diet], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($diet_pid)
    {
        try {
            $diet = $this->dietRepo->searchByPid($diet_pid);
            if (!$diet) {
                return api_response([], false, "Diet not found", 404);
            }
            return api_response($diet, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($diet_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $diet = $this->dietRepo->searchByPid($diet_pid);
            if (!$diet) {
                return api_response([], false, "Diet not found", 404);
            }
            $diet = $this->dietRepo->update($diet->id, $validated);
            if (!$diet) {
                return api_response([], false, "Diet not updated", 500);
            }
            return api_response(["diet" => $diet], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($diet_pid)
    {
        try {
            $diet = $this->dietRepo->searchByPid($diet_pid);
            if (!$diet) {
                return api_response([], false, "Diet not found", 404);
            }
            $delete = $this->dietRepo->delete($diet);
            if (!$delete) {
                return api_response([], false, "Diet not deleted", 500);
            }
            return api_response([], true, "Diet deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
