<?php

namespace App\Repositories;

use App\Models\LabTest;
use App\Models\LabTestCategory;

class LabTestRepositories
{
    public function list($filter = [])
    {
        $query = LabTest::with(['category'])->orderBy('id', 'desc');

        if (!empty($filter['category_pid'])) {
            $query->whereHas('category', function ($q) use ($filter) {
                $q->where('pid', $filter['category_pid']);
            });
        }

        return api_list($query, $filter, ['code', 'name', 'category.name']);
    }

    public function searchByPid($pid)
    {
        try {
            $test = LabTest::with(['category'])->where('pid', $pid)->first();

            if (!$test) {
                return [];
            }

            return $test;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveCategoryId($data);
            $test = LabTest::create($data);
            return $test->load('category');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($test_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveCategoryId($data);
            $test = LabTest::findOrFail($test_id);
            $test->update($data);

            return $test->load('category');
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

    private function resolveCategoryId($data)
    {
        if (!empty($data['category_pid'])) {
            $data['category_id'] = LabTestCategory::where('pid', $data['category_pid'])->firstOrFail()->id;
            unset($data['category_pid']);
        }

        return $data;
    }
}
