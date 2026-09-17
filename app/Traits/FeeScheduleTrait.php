<?php

namespace App\Traits;

use App\Http\Requests\FeeSchedule\StoreRequest;
use Illuminate\Http\Request;

trait FeeScheduleTrait
{
    public function list(Request $request)
    {
        try {
            $schedules = $this->feeScheduleRepo->list($request->only(['fee_category_pid', 'search', 'per_page', 'page']));
            return api_list_response($schedules['items'], $schedules['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $schedule = $this->feeScheduleRepo->store($validated);
            return api_response(["schedule" => $schedule], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($schedule_pid)
    {
        try {
            $schedule = $this->feeScheduleRepo->searchByPid($schedule_pid);
            if (!$schedule) {
                return api_response([], false, "Fee schedule not found", 404);
            }
            return api_response($schedule, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($schedule_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $schedule = $this->feeScheduleRepo->searchByPid($schedule_pid);
            if (!$schedule) {
                return api_response([], false, "Fee schedule not found", 404);
            }
            $schedule = $this->feeScheduleRepo->update($schedule->id, $validated);
            if (!$schedule) {
                return api_response([], false, "Fee schedule not updated", 500);
            }
            return api_response(["schedule" => $schedule], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($schedule_pid)
    {
        try {
            $schedule = $this->feeScheduleRepo->searchByPid($schedule_pid);
            if (!$schedule) {
                return api_response([], false, "Fee schedule not found", 404);
            }
            $delete = $this->feeScheduleRepo->delete($schedule);
            if (!$delete) {
                return api_response([], false, "Fee schedule not deleted", 500);
            }
            return api_response([], true, "Fee schedule deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
