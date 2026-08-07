<?php

namespace App\Traits;

trait DashboardTrait
{
    public function summary()
    {
        try {
            $summary = $this->dashboardRepo->getSummary();
            return api_response($summary, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
