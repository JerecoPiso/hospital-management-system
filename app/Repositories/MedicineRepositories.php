<?php

namespace App\Repositories;

use App\Models\Medicine;

class MedicineRepositories
{

    public function list($filter = [])
    {
        $query = Medicine::orderBy('id', 'desc');

        return api_list($query, $filter, ['name', 'generic_name', 'brand_name', 'dosage_unit', 'form', 'administration_route']);
    }
    public function searchByPid($pid)
    {
        try {

            $med = Medicine::where('pid', $pid)->first();

            if (!$med) {
                return [];
            }

            return $med;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {

            $med = Medicine::create($data);
            return $med;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($med_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $med = Medicine::findOrFail($med_id);
            $med->update($data);

            return $med;
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
