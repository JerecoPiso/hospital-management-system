<?php

namespace App\Traits;

use App\Http\Requests\Floor\StoreRequest;
use Illuminate\Http\Request;

trait FloorTrait
{
    public function list(Request $request)
    {
        try {
            $floors = $this->floorRepo->list($request->only(['building_pid']));
            return api_response($floors, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $floor = $this->floorRepo->store($validated);
            return api_response(["floor" => $floor], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($floor_pid)
    {
        try {
            $floor = $this->floorRepo->searchByPid($floor_pid);
            if (!$floor) {
                return api_response([], false, "Floor not found", 404);
            }
            return api_response($floor, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($floor_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $floor = $this->floorRepo->searchByPid($floor_pid);
            if (!$floor) {
                return api_response([], false, "Floor not found", 404);
            }
            $floor = $this->floorRepo->update($floor->id, $validated);
            if (!$floor) {
                return api_response([], false, "Floor not updated", 500);
            }
            return api_response(["floor" => $floor], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($floor_pid)
    {
        try {
            $floor = $this->floorRepo->searchByPid($floor_pid);
            if (!$floor) {
                return api_response([], false, "Floor not found", 404);
            }
            $delete = $this->floorRepo->delete($floor);
            if (!$delete) {
                return api_response([], false, "Floor not deleted", 500);
            }
            return api_response([], true, "Floor deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
