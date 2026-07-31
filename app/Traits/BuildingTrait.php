<?php

namespace App\Traits;

use App\Http\Requests\Building\StoreRequest;

trait BuildingTrait
{
    public function list()
    {
        try {
            $buildings = $this->buildingRepo->list([]);
            return api_response($buildings, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $building = $this->buildingRepo->store($validated);
            return api_response(["building" => $building], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($building_pid)
    {
        try {
            $building = $this->buildingRepo->searchByPid($building_pid);
            if (!$building) {
                return api_response([], false, "Building not found", 404);
            }
            return api_response($building, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($building_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $building = $this->buildingRepo->searchByPid($building_pid);
            if (!$building) {
                return api_response([], false, "Building not found", 404);
            }
            $building = $this->buildingRepo->update($building->id, $validated);
            if (!$building) {
                return api_response([], false, "Building not updated", 500);
            }
            return api_response(["building" => $building], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($building_pid)
    {
        try {
            $building = $this->buildingRepo->searchByPid($building_pid);
            if (!$building) {
                return api_response([], false, "Building not found", 404);
            }
            $delete = $this->buildingRepo->delete($building);
            if (!$delete) {
                return api_response([], false, "Building not deleted", 500);
            }
            return api_response([], true, "Building deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
