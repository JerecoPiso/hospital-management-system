<?php

namespace App\Traits;

use App\Http\Requests\Station\StoreRequest;
use Illuminate\Http\Request;

trait StationTrait
{
    public function list(Request $request)
    {
        try {
            $stations = $this->stationRepo->list($request->only(['ward_pid']));
            return api_response($stations, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $station = $this->stationRepo->store($validated);
            return api_response(["station" => $station], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($station_pid)
    {
        try {
            $station = $this->stationRepo->searchByPid($station_pid);
            if (!$station) {
                return api_response([], false, "Station not found", 404);
            }
            return api_response($station, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($station_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $station = $this->stationRepo->searchByPid($station_pid);
            if (!$station) {
                return api_response([], false, "Station not found", 404);
            }
            $station = $this->stationRepo->update($station->id, $validated);
            if (!$station) {
                return api_response([], false, "Station not updated", 500);
            }
            return api_response(["station" => $station], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($station_pid)
    {
        try {
            $station = $this->stationRepo->searchByPid($station_pid);
            if (!$station) {
                return api_response([], false, "Station not found", 404);
            }
            $delete = $this->stationRepo->delete($station);
            if (!$delete) {
                return api_response([], false, "Station not deleted", 500);
            }
            return api_response([], true, "Station deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
