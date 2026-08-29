<?php

namespace App\Traits;

use App\Http\Requests\Soap\StoreRequest;
use Illuminate\Http\Request;

trait SoapTrait
{
    public function list(Request $request)
    {
        try {
            $filters = [];
            if ($request->has('patient_case_pid') || filled($request->input('patient_case_pid'))) {
                $patientCase = $this->patientCaseRepo->searchByPid($request->input('patient_case_pid'));
                if ($patientCase) {
                    $filters['patient_case_id'] = $patientCase->id;
                }
            }
            $soaps = $this->soapRepo->list($filters);
            return api_response($soaps, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["doctor_id"] = auth()->id();
            $soap = $this->soapRepo->store($validated);
            return api_response(["soap" => $soap], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($soap_pid)
    {
        try {
            $soap = $this->soapRepo->searchByPid($soap_pid);
            if (!$soap) {
                return api_response([], false, "SOAP note not found", 404);
            }
            return api_response($soap, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($soap_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $soap = $this->soapRepo->searchByPid($soap_pid);
            if (!$soap) {
                return api_response([], false, "SOAP note not found", 404);
            }
            $soap = $this->soapRepo->update($soap->id, $validated);
            if (!$soap) {
                return api_response([], false, "SOAP note not updated", 500);
            }
            return api_response(["soap" => $soap], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($soap_pid)
    {
        try {
            $soap = $this->soapRepo->searchByPid($soap_pid);
            if (!$soap) {
                return api_response([], false, "SOAP note not found", 404);
            }
            $delete = $this->soapRepo->delete($soap);
            if (!$delete) {
                return api_response([], false, "SOAP note not deleted", 500);
            }
            return api_response([], true, "SOAP note deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
