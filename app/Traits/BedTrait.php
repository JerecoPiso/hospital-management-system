<?php

namespace App\Traits;

use App\Http\Requests\Bed\StoreRequest;
use Illuminate\Http\Request;

trait BedTrait
{
    public function list(Request $request)
    {
        try {
            $beds = $this->bedRepo->list($request->only(['room_pid', 'search', 'per_page', 'page']));
            return api_list_response($beds['items'], $beds['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $bed = $this->bedRepo->store($validated);
            return api_response(["bed" => $bed], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($bed_pid)
    {
        try {
            $bed = $this->bedRepo->searchByPid($bed_pid);
            if (!$bed) {
                return api_response([], false, "Bed not found", 404);
            }
            return api_response($bed, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($bed_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $bed = $this->bedRepo->searchByPid($bed_pid);
            if (!$bed) {
                return api_response([], false, "Bed not found", 404);
            }
            $bed = $this->bedRepo->update($bed->id, $validated);
            if (!$bed) {
                return api_response([], false, "Bed not updated", 500);
            }
            return api_response(["bed" => $bed], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($bed_pid)
    {
        try {
            $bed = $this->bedRepo->searchByPid($bed_pid);
            if (!$bed) {
                return api_response([], false, "Bed not found", 404);
            }
            $delete = $this->bedRepo->delete($bed);
            if (!$delete) {
                return api_response([], false, "Bed not deleted", 500);
            }
            return api_response([], true, "Bed deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
