<?php

namespace App\Traits;

use App\Http\Requests\LabTestCategory\StoreRequest;
use Illuminate\Http\Request;

trait LabTestCategoryTrait
{
    public function list(Request $request)
    {
        try {
            $categories = $this->labTestCategoryRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($categories['items'], $categories['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $category = $this->labTestCategoryRepo->store($validated);
            return api_response(["category" => $category], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($category_pid)
    {
        try {
            $category = $this->labTestCategoryRepo->searchByPid($category_pid);
            if (!$category) {
                return api_response([], false, "Lab test category not found", 404);
            }
            return api_response($category, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($category_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $category = $this->labTestCategoryRepo->searchByPid($category_pid);
            if (!$category) {
                return api_response([], false, "Lab test category not found", 404);
            }
            $category = $this->labTestCategoryRepo->update($category->id, $validated);
            if (!$category) {
                return api_response([], false, "Lab test category not updated", 500);
            }
            return api_response(["category" => $category], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($category_pid)
    {
        try {
            $category = $this->labTestCategoryRepo->searchByPid($category_pid);
            if (!$category) {
                return api_response([], false, "Lab test category not found", 404);
            }
            $delete = $this->labTestCategoryRepo->delete($category);
            if (!$delete) {
                return api_response([], false, "Lab test category not deleted", 500);
            }
            return api_response([], true, "Lab test category deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
