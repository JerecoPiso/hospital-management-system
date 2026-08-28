<?php

namespace App\Traits;

use App\Http\Requests\SupplyStock\StoreRequest;
use Illuminate\Http\Request;

trait SupplyStockTrait
{
    public function list(Request $request)
    {
        try {
            $supplyStocks = $this->supplyStockRepo->list($request->only(['supply_pid', 'search', 'per_page', 'page']));
            return api_list_response($supplyStocks['items'], $supplyStocks['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $supplyStock = $this->supplyStockRepo->store($validated);
            return api_response(["supply_stock" => $supplyStock], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($supply_stock_pid)
    {
        try {
            $supplyStock = $this->supplyStockRepo->searchByPid($supply_stock_pid);
            if (!$supplyStock) {
                return api_response([], false, "Supply stock not found", 404);
            }
            return api_response($supplyStock, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($supply_stock_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $supplyStock = $this->supplyStockRepo->searchByPid($supply_stock_pid);
            if (!$supplyStock) {
                return api_response([], false, "Supply stock not found", 404);
            }
            $supplyStock = $this->supplyStockRepo->update($supplyStock->id, $validated);
            if (!$supplyStock) {
                return api_response([], false, "Supply stock not updated", 500);
            }
            return api_response(["supply_stock" => $supplyStock], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($supply_stock_pid)
    {
        try {
            $supplyStock = $this->supplyStockRepo->searchByPid($supply_stock_pid);
            if (!$supplyStock) {
                return api_response([], false, "Supply stock not found", 404);
            }
            $delete = $this->supplyStockRepo->delete($supplyStock);
            if (!$delete) {
                return api_response([], false, "Supply stock not deleted", 500);
            }
            return api_response([], true, "Supply stock deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
