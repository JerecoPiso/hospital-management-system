<?php

namespace App\Http\Controllers;

use App\Traits\SupplyDistributionTrait;
use App\Repositories\SupplyDistributionRepositories;

class SupplyDistributionController extends Controller
{
    use SupplyDistributionTrait;

    public $supplyDistributionRepo;
    public function __construct(SupplyDistributionRepositories $supplyDistributionRepo)
    {
        $this->supplyDistributionRepo = $supplyDistributionRepo;
    }
}
