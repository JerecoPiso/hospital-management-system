<?php

namespace App\Repositories;

use App\Models\LabTest;
use App\Models\LabTestParameter;

class LabTestParameterRepositories
{
    public function list($filter = [])
    {
        $query = LabTestParameter::with(['labTest'])->orderBy('id', 'desc');

        if (!empty($filter['lab_test_pid'])) {
            $query->whereHas('labTest', function ($q) use ($filter) {
                $q->where('pid', $filter['lab_test_pid']);
            });
        }

        return api_list($query, $filter, ['parameter_name', 'unit', 'reference_range', 'labTest.name', 'labTest.code']);
    }

    public function searchByPid($pid)
    {
        try {
            $parameter = LabTestParameter::with(['labTest'])->where('pid', $pid)->first();

            if (!$parameter) {
                return [];
            }

            return $parameter;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveLabTestId($data);
            $parameter = LabTestParameter::create($data);
            return $parameter->load('labTest');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($parameter_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveLabTestId($data);
            $parameter = LabTestParameter::findOrFail($parameter_id);
            $parameter->update($data);

            return $parameter->load('labTest');
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

    private function resolveLabTestId($data)
    {
        if (!empty($data['lab_test_pid'])) {
            $data['lab_test_id'] = LabTest::where('pid', $data['lab_test_pid'])->firstOrFail()->id;
            unset($data['lab_test_pid']);
        }

        return $data;
    }
}
