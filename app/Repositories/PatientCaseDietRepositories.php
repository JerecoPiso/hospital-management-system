<?php

namespace App\Repositories;

use App\Models\Diet;
use App\Models\DietsServed;
use App\Models\PatientCase;
use App\Models\PatientCaseDiet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientCaseDietRepositories
{
    private function relations(): array
    {
        return [
            'patientCase.patient',
            'diet',
            'user',
            'dietsServed' => function ($q) {
                $q->with('user')->orderByRaw('COALESCE(served_at, created_at) desc');
            },
        ];
    }

    public function list($filter = [])
    {
        $query = PatientCaseDiet::with($this->relations())
            ->withCount('dietsServed')
            ->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $query->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        return api_list($query, $filter, [
            'remarks',
            'diet.name',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
            'patientCase.patient.medical_record_number',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $patientCaseDiet = PatientCaseDiet::with($this->relations())
                ->withCount('dietsServed')
                ->where('pid', $pid)
                ->first();

            if (!$patientCaseDiet) {
                return [];
            }

            return $patientCaseDiet;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
                $diet = Diet::where('pid', $data['diet_pid'])->firstOrFail();

                $patientCaseDiet = PatientCaseDiet::create([
                    'patient_case_id' => $patientCase->id,
                    'diet_id' => $diet->id,
                    'user_id' => $data['user_id'],
                    'remarks' => $data['remarks'] ?? null,
                ]);

                return $patientCaseDiet->load($this->relations());
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update($patient_case_diet_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            return DB::transaction(function () use ($patient_case_diet_id, $data) {
                $patientCaseDiet = PatientCaseDiet::findOrFail($patient_case_diet_id);

                $update = [
                    'remarks' => $data['remarks'] ?? null,
                ];

                if (!empty($data['patient_case_pid'])) {
                    $update['patient_case_id'] = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail()->id;
                }

                if (!empty($data['diet_pid'])) {
                    $update['diet_id'] = Diet::where('pid', $data['diet_pid'])->firstOrFail()->id;
                }

                $patientCaseDiet->update($update);

                return $patientCaseDiet->load($this->relations());
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->dietsServed()->delete();
            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function serve($patientCaseDiet, $data)
    {
        try {
            $served = DietsServed::create([
                'patient_case_diet_id' => $patientCaseDiet->id,
                'user_id' => $data['user_id'],
                'served_at' => !empty($data['served_at'])
                    ? Carbon::parse($data['served_at'])->format('Y-m-d H:i:s')
                    : now(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            return $served->load('user');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
