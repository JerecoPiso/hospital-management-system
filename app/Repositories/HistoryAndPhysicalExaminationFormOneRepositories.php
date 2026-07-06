<?php

namespace App\Repositories;

use App\Models\HistoryAndPhysicalExaminationFormOne;

class HistoryAndPhysicalExaminationFormOneRepositories
{

    public function list($filter = [])
    {
        $history = HistoryAndPhysicalExaminationFormOne::with(['user'])->orderBy('id', 'desc');
        $history = $history->get();
        return $history->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $history = HistoryAndPhysicalExaminationFormOne::where('pid', $pid)->first();

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

            $history = HistoryAndPhysicalExaminationFormOne::create($data);
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

            $history = HistoryAndPhysicalExaminationFormOne::findOrFail($history_id);
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
