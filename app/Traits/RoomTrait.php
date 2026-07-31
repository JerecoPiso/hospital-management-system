<?php

namespace App\Traits;

use App\Http\Requests\Room\StoreRequest;
use Illuminate\Http\Request;

trait RoomTrait
{
    public function list(Request $request)
    {
        try {
            $rooms = $this->roomRepo->list($request->only(['ward_pid']));
            return api_response($rooms, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $room = $this->roomRepo->store($validated);
            return api_response(["room" => $room], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($room_pid)
    {
        try {
            $room = $this->roomRepo->searchByPid($room_pid);
            if (!$room) {
                return api_response([], false, "Room not found", 404);
            }
            return api_response($room, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($room_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $room = $this->roomRepo->searchByPid($room_pid);
            if (!$room) {
                return api_response([], false, "Room not found", 404);
            }
            $room = $this->roomRepo->update($room->id, $validated);
            if (!$room) {
                return api_response([], false, "Room not updated", 500);
            }
            return api_response(["room" => $room], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($room_pid)
    {
        try {
            $room = $this->roomRepo->searchByPid($room_pid);
            if (!$room) {
                return api_response([], false, "Room not found", 404);
            }
            $delete = $this->roomRepo->delete($room);
            if (!$delete) {
                return api_response([], false, "Room not deleted", 500);
            }
            return api_response([], true, "Room deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
