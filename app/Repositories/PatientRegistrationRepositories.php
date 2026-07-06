<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientRegistrationRepositories
{

    public function list($filter = [])
    {
        $patient = Patient::with(['patientCases'])->orderBy('id', 'desc');
        $patient = $patient->get();
        return $patient->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $patient = Patient::with(['patientCases'])->where('pid', $pid)->first();

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
                    'station_id' => null,
                    'bed_id' => null,
                    'patient_type_id' => null,
                    'case_number' => 'CASE-' . strtoupper(Str::random(8)),
                    'admission_datetime' => Carbon::parse($data['admission_datetime'])->toDateTimeString(),
                    'chief_complaint' => $data['chief_complaint'],
                    'initial_diagnosis' => $data['initial_diagnosis'] ?? null,
                    'final_diagnosis' => $data['final_diagnosis'] ?? null,
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
                'birthdate' => $data['birthdate'],
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
                    'admission_datetime' => $data['admission_datetime'],
                    'chief_complaint' => $data['chief_complaint'],
                    'initial_diagnosis' => $data['initial_diagnosis'] ?? null,
                    'final_diagnosis' => $data['final_diagnosis'] ?? null,
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
