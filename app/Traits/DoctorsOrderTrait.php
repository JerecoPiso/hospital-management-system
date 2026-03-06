<?php

namespace App\Traits;

use App\Http\Requests\DoctorsOrder\StoreRequest;

trait DoctorsOrderTrait
{
    public function list()
    {
        try {
            $orders = $this->doctorsOrderRepo->list([]);
            return api_response($orders, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = auth()->id();
            $order = $this->doctorsOrderRepo->store($validated);
            return api_response(["order" => $order], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($order_pid)
    {
        try {
            $order = $this->doctorsOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Order not found", 404);
            }
            return api_response( $order, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($order_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $order = $this->doctorsOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Order not found", 404);
            }
            $order = $this->doctorsOrderRepo->update($order->id, $validated);
            if (!$order) {
                return api_response([], false, "Order not updated", 500);
            }
            return api_response(["order" => $order], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($order_pid)
    {
        try {
            $order = $this->doctorsOrderRepo->searchByPid($order_pid);
            if (!$order) {
                return api_response([], false, "Order not found", 404);
            }
            $delete = $this->doctorsOrderRepo->delete($order);
            if (!$delete) {
                return api_response([], false, "Order not deleted", 500);
            }
            return api_response([], true, "Order deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
