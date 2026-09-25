<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ReportTrait
{
    public function dispenseMedicineStocks(Request $request)
    {
        try {
            $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
            ]);
            $report = $this->reportRepo->dispenseMedicineStocks($request->only(['date_from', 'date_to']));
            return api_response($report, true, "Success", 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return api_response(['errors' => $e->errors()], false, 'Invalid data.', 422);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function patientInvoice(Request $request)
    {
        try {
            $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'case_type' => 'nullable|in:outpatient,inpatient',
                'status' => 'nullable|in:unpaid,partially_paid,paid',
            ]);
            $report = $this->reportRepo->patientInvoice($request->only(['date_from', 'date_to', 'case_type', 'status']));
            return api_response($report, true, "Success", 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return api_response(['errors' => $e->errors()], false, 'Invalid data.', 422);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
