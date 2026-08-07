<?php

namespace App\Traits;

use App\Http\Requests\MedicineDistribution\StoreRequest;
use Illuminate\Http\Request;

trait MedicineDistributionTrait
{
    public function list(Request $request)
    {
        try {
            $medicineDistributions = $this->medicineDistributionRepo->list($request->only(['medicine_stock_pid']));
            return api_response($medicineDistributions, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $medicineDistribution = $this->medicineDistributionRepo->store($validated);
            return api_response(["medicine_distribution" => $medicineDistribution], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($medicine_distribution_pid)
    {
        try {
            $medicineDistribution = $this->medicineDistributionRepo->searchByPid($medicine_distribution_pid);
            if (!$medicineDistribution) {
                return api_response([], false, "Medicine distribution not found", 404);
            }
            return api_response($medicineDistribution, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
