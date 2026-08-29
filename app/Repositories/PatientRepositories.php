<?php

namespace App\Repositories;

use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientType;
use App\Models\Station;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientRepositories
{
    private function resolvePatientTypeId($data)
    {
        if (empty($data['patient_type_pid'])) {
            return null;
        }

        return PatientType::where('pid', $data['patient_type_pid'])->value('id');
    }

    private function resolveStationId($data)
    {
        if (($data['type'] ?? null) !== 'inpatient' || empty($data['station_pid'])) {
            return null;
        }

        return Station::where('pid', $data['station_pid'])->value('id');
    }

    private function resolveBedId($data)
    {
        if (($data['type'] ?? null) !== 'inpatient' || empty($data['bed_pid'])) {
            return null;
        }

        return Bed::where('pid', $data['bed_pid'])->value('id');
    }


    public function list($filter = [])
    {
        $patient = Patient::with(['patientCases.patientType', 'patientCases.station', 'patientCases.bed'])->orderBy('id', 'desc');

        if (!empty($filter['type'])) {
            $patient->whereHas('patientCases', function ($q) use ($filter) {
                $q->where('type', $filter['type']);
            });
        }

        return api_list($patient, $filter, [
            'firstname',
            'lastname',
            'middlename',
            'medical_record_number',
            'contact_number',
            'email_address',
        ]);
    }
    public function searchByPid($pid)
    {
        try {

            $patient = Patient::with(['patientCases.patientType', 'patientCases.station', 'patientCases.bed'])->where('pid', $pid)->first();

            if (!$patient) {
                return [];
            }

            return $patient;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {

            $patient = DB::transaction(function () use ($data) {
                $patient = Patient::create([
                    'medical_record_number' => 'MRN-' . strtoupper(Str::random(8)),
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'middlename' => $data['middlename'] ?? null,
                    'suffix' => $data['suffix'] ?? null,
                    'birthdate' => Carbon::parse($data['birthdate'])->toDateString(),
                    'gender' => $data['gender'] ?? null,
                    'civil_status' => $data['civil_status'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'email_address' => $data['email_address'] ?? null,
                    'religion' => $data['religion'] ?? null,
                    'birthplace' => $data['birthplace'] ?? null,
                    'occupation' => $data['occupation'] ?? null,
                    'spouse_name' => $data['spouse_name'] ?? null,
                ]);

                $patient->patientCases()->create([
                    'pid' => Str::uuid()->toString(),
                    'station_id' => $this->resolveStationId($data),
                    'bed_id' => $this->resolveBedId($data),
                    'patient_type_id' => $this->resolvePatientTypeId($data),
                    'case_number' => 'CASE-' . strtoupper(Str::random(8)),
                    'admission_datetime' => Carbon::parse($data['admission_datetime'])->toDateTimeString(),
                    'chief_complaint' => $data['chief_complaint'],
                    'initial_diagnosis' => $data['initial_diagnosis'] ?? null,
                    'final_diagnosis' => $data['final_diagnosis'] ?? null,
                    'type' => $data['type'],
                ]);

                return $patient;
            });

            return $patient->load('patientCases');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($patient_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $patient = Patient::findOrFail($patient_id);
            $patient->update([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'middlename' => $data['middlename'] ?? null,
                'suffix' => $data['suffix'] ?? null,
                'birthdate' => Carbon::parse($data['birthdate'])->toDateString(),
                'gender' => $data['gender'] ?? null,
                'civil_status' => $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'email_address' => $data['email_address'] ?? null,
                'religion' => $data['religion'] ?? null,
                'birthplace' => $data['birthplace'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'spouse_name' => $data['spouse_name'] ?? null,
            ]);

            $patientCase = $patient->patientCases()->latest('id')->first();
            if ($patientCase) {
                $patientCase->update([
                    'admission_datetime' => Carbon::parse($data['admission_datetime'])->toDateTimeString(),
                    'chief_complaint' => $data['chief_complaint'],
                    'initial_diagnosis' => $data['initial_diagnosis'] ?? null,
                    'final_diagnosis' => $data['final_diagnosis'] ?? null,
                    'type' => $data['type'],
                    'patient_type_id' => $this->resolvePatientTypeId($data),
                    'station_id' => $this->resolveStationId($data),
                    'bed_id' => $this->resolveBedId($data),
                ]);
            }

            return $patient->load('patientCases');
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

            $data->patientCases()->delete();
            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
