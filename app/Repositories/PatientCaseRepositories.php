<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\PatientCase;
use App\Models\PatientType;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientCaseRepositories
{
    public function searchByPid($pid)
    {
        try {
            $patientCase = PatientCase::with(['patient', 'patientType'])->where('pid', $pid)->first();

            if (!$patientCase) {
                return [];
            }

            return $patientCase;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $patient = Patient::where('pid', $data['patient_pid'])->firstOrFail();

            $patientTypeId = !empty($data['patient_type_pid'])
                ? PatientType::where('pid', $data['patient_type_pid'])->value('id')
                : null;

            $patientCase = $patient->patientCases()->create([
                'pid' => Str::uuid()->toString(),
                'station_id' => null,
                'bed_id' => null,
                'patient_type_id' => $patientTypeId,
                'case_number' => 'CASE-' . strtoupper(Str::random(8)),
                'admission_datetime' => Carbon::parse($data['admission_datetime'])->toDateTimeString(),
                'chief_complaint' => $data['chief_complaint'],
                'initial_diagnosis' => $data['initial_diagnosis'] ?? null,
                'final_diagnosis' => $data['final_diagnosis'] ?? null,
                'type' => $data['type'],
            ]);

            return $patientCase->load(['patient', 'patientType']);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
