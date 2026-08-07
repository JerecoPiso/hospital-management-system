<?php

namespace App\Traits;

use App\Http\Requests\MedicineStock\StoreRequest;
use Illuminate\Http\Request;

trait MedicineStockTrait
{
    public function list(Request $request)
    {
        try {
            $medicineStocks = $this->medicineStockRepo->list($request->only(['medicine_pid']));
            return api_response($medicineStocks, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $medicineStock = $this->medicineStockRepo->store($validated);
            return api_response(["medicine_stock" => $medicineStock], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($medicine_stock_pid)
    {
        try {
            $medicineStock = $this->medicineStockRepo->searchByPid($medicine_stock_pid);
            if (!$medicineStock) {
                return api_response([], false, "Medicine stock not found", 404);
            }
            return api_response($medicineStock, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($medicine_stock_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $medicineStock = $this->medicineStockRepo->searchByPid($medicine_stock_pid);
            if (!$medicineStock) {
                return api_response([], false, "Medicine stock not found", 404);
            }
            $medicineStock = $this->medicineStockRepo->update($medicineStock->id, $validated);
            if (!$medicineStock) {
                return api_response([], false, "Medicine stock not updated", 500);
            }
            return api_response(["medicine_stock" => $medicineStock], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($medicine_stock_pid)
    {
        try {
            $medicineStock = $this->medicineStockRepo->searchByPid($medicine_stock_pid);
            if (!$medicineStock) {
                return api_response([], false, "Medicine stock not found", 404);
            }
            $delete = $this->medicineStockRepo->delete($medicineStock);
            if (!$delete) {
                return api_response([], false, "Medicine stock not deleted", 500);
            }
            return api_response([], true, "Medicine stock deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
