<?php

namespace App\Traits;

use App\Http\Requests\SupplyMovement\StoreRequest;
use Illuminate\Http\Request;

trait SupplyMovementTrait
{
    public function list(Request $request)
    {
        try {
            $supplyMovements = $this->supplyMovementRepo->list($request->only(['supply_stock_pid']));
            return api_response($supplyMovements, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $supplyMovement = $this->supplyMovementRepo->store($validated);
            return api_response(["supply_movement" => $supplyMovement], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($supply_movement_pid)
    {
        try {
            $supplyMovement = $this->supplyMovementRepo->searchByPid($supply_movement_pid);
            if (!$supplyMovement) {
                return api_response([], false, "Supply movement not found", 404);
            }
            return api_response($supplyMovement, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
