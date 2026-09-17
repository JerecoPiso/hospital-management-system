<?php

namespace App\Traits;

use App\Http\Requests\RadiologyOrder\ReportRequest;
use App\Http\Requests\RadiologyOrder\StatusRequest;
use App\Http\Requests\RadiologyOrder\StoreRequest;
use Illuminate\Http\Request;

trait RadiologyOrderTrait
{
    public function list(Request $request)
    {
        try {
            $orders = $this->radiologyOrderRepo->list($request->only(['patient_case_pid', 'status', 'search', 'per_page', 'page']));
            return api_list_response($orders['items'], $orders['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['doctor_id'] = auth()->id();

            $order = $this->radiologyOrderRepo->store($validated);
            return api_response(["radiology_order" => $order], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($order_pid)
    {
        try {
            $order = $this->radiologyOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Radiology order not found", 404);
            }
            return api_response($order, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateStatus($order_pid, StatusRequest $request)
    {
        try {
            $validated = $request->validated();
            $order = $this->radiologyOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Radiology order not found", 404);
            }
            $order = $this->radiologyOrderRepo->updateStatus($order->id, $validated);
            return api_response(["radiology_order" => $order], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function saveReport($order_pid, ReportRequest $request)
    {
        try {
            $validated = $request->validated();
            $order = $this->radiologyOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Radiology order not found", 404);
            }
            $order = $this->radiologyOrderRepo->saveReport($order->id, $validated);
            return api_response(["radiology_order" => $order], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($order_pid)
    {
        try {
            $order = $this->radiologyOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Radiology order not found", 404);
            }
            $delete = $this->radiologyOrderRepo->delete($order);
            if (!$delete) {
                return api_response([], false, "Radiology order not deleted", 500);
            }
            return api_response([], true, "Radiology order deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
