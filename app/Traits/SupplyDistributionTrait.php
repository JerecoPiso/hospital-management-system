<?php

namespace App\Traits;

use App\Http\Requests\SupplyDistribution\StoreRequest;
use Illuminate\Http\Request;

trait SupplyDistributionTrait
{
    public function list(Request $request)
    {
        try {
            $supplyDistributions = $this->supplyDistributionRepo->list($request->only(['supply_stock_pid']));
            return api_response($supplyDistributions, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $supplyDistribution = $this->supplyDistributionRepo->store($validated);
            return api_response(["supply_distribution" => $supplyDistribution], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($supply_distribution_pid)
    {
        try {
            $supplyDistribution = $this->supplyDistributionRepo->searchByPid($supply_distribution_pid);
            if (!$supplyDistribution) {
                return api_response([], false, "Supply distribution not found", 404);
            }
            return api_response($supplyDistribution, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
