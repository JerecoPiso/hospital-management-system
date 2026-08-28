<?php

namespace App\Traits;

use App\Http\Requests\Prescription\StatusRequest;
use App\Http\Requests\Prescription\StoreRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
trait PrescriptionTrait
{
    public function list(Request $request)
    {
        try {
            $prescriptions = $this->prescriptionRepo->list($request->only(['patient_case_pid', 'status', 'search', 'per_page', 'page']));
            return api_list_response($prescriptions['items'], $prescriptions['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["doctor_id"] = auth()->id();
            $validated["prescription_date"] = Carbon::parse($validated["prescription_date"])->format('Y-m-d H:i:s');

            $prescription = $this->prescriptionRepo->store($validated);
            return api_response(["prescription" => $prescription], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($prescription_pid)
    {
        try {
            $prescription = $this->prescriptionRepo->searchByPid($prescription_pid);
            if (!$prescription) {
                return api_response([], false, "Prescription not found", 404);
            }
            return api_response($prescription, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($prescription_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $prescription = $this->prescriptionRepo->searchByPid($prescription_pid);
            if (!$prescription) {
                return api_response([], false, "Prescription not found", 404);
            }
            $prescription = $this->prescriptionRepo->update($prescription->id, $validated);
            if (!$prescription) {
                return api_response([], false, "Prescription not updated", 500);
            }
            return api_response(["prescription" => $prescription], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateStatus($prescription_pid, StatusRequest $request)
    {
        try {
            $validated = $request->validated();
            $prescription = $this->prescriptionRepo->searchByPid($prescription_pid);
            if (!$prescription) {
                return api_response([], false, "Prescription not found", 404);
            }
            $prescription = $this->prescriptionRepo->updateStatus($prescription->id, $validated);
            if (!$prescription) {
                return api_response([], false, "Prescription status not updated", 500);
            }
            return api_response(["prescription" => $prescription], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($prescription_pid)
    {
        try {
            $prescription = $this->prescriptionRepo->searchByPid($prescription_pid);
            if (!$prescription) {
                return api_response([], false, "Prescription not found", 404);
            }
            $delete = $this->prescriptionRepo->delete($prescription);
            if (!$delete) {
                return api_response([], false, "Prescription not deleted", 500);
            }
            return api_response([], true, "Prescription deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
