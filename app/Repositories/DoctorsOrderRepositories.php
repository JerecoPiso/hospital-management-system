<?php

namespace App\Repositories;

use App\Models\DoctorsOrder;
use App\Models\PatientCase;
use Illuminate\Support\Facades\DB;

class DoctorsOrderRepositories
{

    public function list($filter = [])
    {
        $order = DoctorsOrder::with(['doctor', 'patientCase.patient'])->orderBy('id', 'desc');

        if (!empty($filter['patient_case_id'])) {
            $order->where('patient_case_id', $filter['patient_case_id']);
        }

        $order = $order->get();
        return $order->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $order = DoctorsOrder::where('pid', $pid)->first();

            if (!$order) {
                return [];
            }

            return $order;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
                unset($data['patient_case_pid']);
                $data['patient_case_id'] = $patientCase->id;

                $order = DoctorsOrder::create($data);
                return $order->load(['patientCase.patient']);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($order_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $order = DoctorsOrder::findOrFail($order_id);

            if (!empty($data['patient_case_pid'])) {
                $data['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
            }
            unset($data['patient_case_pid']);

            $order->update($data);

            return $order;
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
