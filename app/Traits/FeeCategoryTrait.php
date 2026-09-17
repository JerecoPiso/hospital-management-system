<?php

namespace App\Traits;

use App\Http\Requests\FeeCategory\StoreRequest;
use Illuminate\Http\Request;

trait FeeCategoryTrait
{
    public function list(Request $request)
    {
        try {
            $categories = $this->feeCategoryRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($categories['items'], $categories['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $category = $this->feeCategoryRepo->store($validated);
            return api_response(["category" => $category], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($category_pid)
    {
        try {
            $category = $this->feeCategoryRepo->searchByPid($category_pid);
            if (!$category) {
                return api_response([], false, "Fee category not found", 404);
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
            $category = $this->feeCategoryRepo->searchByPid($category_pid);
            if (!$category) {
                return api_response([], false, "Fee category not found", 404);
            }
            $category = $this->feeCategoryRepo->update($category->id, $validated);
            if (!$category) {
                return api_response([], false, "Fee category not updated", 500);
            }
            return api_response(["category" => $category], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($category_pid)
    {
        try {
            $category = $this->feeCategoryRepo->searchByPid($category_pid);
            if (!$category) {
                return api_response([], false, "Fee category not found", 404);
            }
            $delete = $this->feeCategoryRepo->delete($category);
            if (!$delete) {
                return api_response([], false, "Fee category not deleted", 500);
            }
            return api_response([], true, "Fee category deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
