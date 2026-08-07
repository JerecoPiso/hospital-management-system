<?php

namespace App\Traits;

use App\Http\Requests\MedicineStockMovement\StoreRequest;
use Illuminate\Http\Request;

trait MedicineStockMovementTrait
{
    public function list(Request $request)
    {
        try {
            $medicineStockMovements = $this->medicineStockMovementRepo->list($request->only(['medicine_pid']));
            return api_response($medicineStockMovements, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $medicineStockMovement = $this->medicineStockMovementRepo->store($validated);
            return api_response(["medicine_stock_movement" => $medicineStockMovement], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($medicine_stock_movement_pid)
    {
        try {
            $medicineStockMovement = $this->medicineStockMovementRepo->searchByPid($medicine_stock_movement_pid);
            if (!$medicineStockMovement) {
                return api_response([], false, "Medicine stock movement not found", 404);
            }
            return api_response($medicineStockMovement, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
