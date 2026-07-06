<?php

namespace App\Repositories;

use App\Models\HistoryAndPhysicalExaminationFormTwo;

class HistoryAndPhysicalExaminationFormTwoRepositories
{

    public function list($filter = [])
    {
        $history = HistoryAndPhysicalExaminationFormTwo::with(['user'])->orderBy('id', 'desc');
        $history = $history->get();
        return $history->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $history = HistoryAndPhysicalExaminationFormTwo::where('pid', $pid)->first();

            if (!$history) {
                return [];
            }

            return $history;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {

            $history = HistoryAndPhysicalExaminationFormTwo::create($data);
            return $history;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($history_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $history = HistoryAndPhysicalExaminationFormTwo::findOrFail($history_id);
            $history->update($data);

            return $history;
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
}
