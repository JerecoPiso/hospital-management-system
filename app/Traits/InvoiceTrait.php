<?php

namespace App\Traits;

use App\Http\Requests\Invoice\GenerateRequest;
use App\Http\Requests\Invoice\PaymentRequest;
use Illuminate\Http\Request;

trait InvoiceTrait
{
    public function list(Request $request)
    {
        try {
            $invoices = $this->invoiceRepo->list($request->only(['patient_case_pid', 'status', 'search', 'per_page', 'page']));
            return api_list_response($invoices['items'], $invoices['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function generate(GenerateRequest $request)
    {
        try {
            $validated = $request->validated();
            $invoice = $this->invoiceRepo->generate($validated);
            return api_response(["invoice" => $invoice], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function charges(Request $request)
    {
        try {
            $request->validate(['patient_case_pid' => 'required|string']);
            $charges = $this->invoiceRepo->charges($request->patient_case_pid);
            return api_response($charges, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($invoice_pid)
    {
        try {
            $invoice = $this->invoiceRepo->searchByPid($invoice_pid);
            if (!$invoice) {
                return api_response([], false, "Invoice not found", 404);
            }
            return api_response($invoice, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function pay($invoice_pid, PaymentRequest $request)
    {
        try {
            $validated = $request->validated();
            $invoice = $this->invoiceRepo->searchByPid($invoice_pid);
            if (!$invoice) {
                return api_response([], false, "Invoice not found", 404);
            }
            $invoice = $this->invoiceRepo->pay($invoice->id, $validated);
            return api_response(["invoice" => $invoice], true, "Payment recorded successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
