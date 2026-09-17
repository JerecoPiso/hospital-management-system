<?php

namespace App\Repositories;

use App\Models\FeeCategory;

class FeeCategoryRepositories
{
    public function list($filter = [])
    {
        $query = FeeCategory::orderBy('id', 'desc');

        return api_list($query, $filter, ['name', 'description']);
    }

    public function searchByPid($pid)
    {
        try {
            $category = FeeCategory::where('pid', $pid)->first();

            if (!$category) {
                return [];
            }

            return $category;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $category = FeeCategory::create($data);
            return $category;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($category_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $category = FeeCategory::findOrFail($category_id);
            $category->update($data);

            return $category;
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
