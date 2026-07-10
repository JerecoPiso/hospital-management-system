<?php

namespace App\Repositories;

use App\Models\VitalSign;

class VitalSignRepositories
{

    public function list($filter = [])
    {
        $vitalSign = VitalSign::with(['user'])->orderBy('id', 'desc');
        $vitalSign = $vitalSign->get();
        return $vitalSign->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $vitalSign = VitalSign::where('pid', $pid)->first();

            if (!$vitalSign) {
                return [];
            }

            return $vitalSign;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {

            $vitalSign = VitalSign::create($data);
            return $vitalSign;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($vital_sign_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $vitalSign = VitalSign::findOrFail($vital_sign_id);
            $vitalSign->update($data);

            return $vitalSign;
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
