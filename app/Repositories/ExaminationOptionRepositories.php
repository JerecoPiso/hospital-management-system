<?php

namespace App\Repositories;

use App\Models\ExaminationOption;

class ExaminationOptionRepositories
{
    public function list($filter = [])
    {
        $query = ExaminationOption::orderBy('type', 'asc')->orderBy('id', 'asc');

        if (!empty($filter['type'])) {
            $query->where('type', $filter['type']);
        }

        if (isset($filter['active']) && $filter['active'] !== '') {
            $query->where('active', filter_var($filter['active'], FILTER_VALIDATE_BOOLEAN));
        }

        return api_list($query, $filter, ['code', 'description']);
    }

    public function searchByPid($pid)
    {
        try {
            $item = ExaminationOption::where('pid', $pid)->first();

            if (!$item) {
                return [];
            }

            return $item;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return ExaminationOption::create($data);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $item = ExaminationOption::findOrFail($id);
            $item->update($data);

            return $item;
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
