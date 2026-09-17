<?php

namespace App\Repositories;

use App\Models\FeeCategory;
use App\Models\FeeSchedule;

class FeeScheduleRepositories
{
    public function list($filter = [])
    {
        $query = FeeSchedule::with(['feeCategory'])->orderBy('id', 'desc');

        if (!empty($filter['fee_category_pid'])) {
            $query->whereHas('feeCategory', function ($q) use ($filter) {
                $q->where('pid', $filter['fee_category_pid']);
            });
        }

        return api_list($query, $filter, ['code', 'name', 'feeCategory.name']);
    }

    public function searchByPid($pid)
    {
        try {
            $schedule = FeeSchedule::with(['feeCategory'])->where('pid', $pid)->first();

            if (!$schedule) {
                return [];
            }

            return $schedule;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveFeeCategoryId($data);
            $schedule = FeeSchedule::create($data);
            return $schedule->load('feeCategory');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($schedule_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveFeeCategoryId($data);
            $schedule = FeeSchedule::findOrFail($schedule_id);
            $schedule->update($data);

            return $schedule->load('feeCategory');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    private function resolveFeeCategoryId($data)
    {
        if (!empty($data['fee_category_pid'])) {
            $data['fee_category_id'] = FeeCategory::where('pid', $data['fee_category_pid'])->firstOrFail()->id;
            unset($data['fee_category_pid']);
        }

        return $data;
    }
}
