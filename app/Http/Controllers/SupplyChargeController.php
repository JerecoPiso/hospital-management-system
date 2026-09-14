<?php

namespace App\Http\Controllers;

use App\Traits\SupplyChargeTrait;
use App\Repositories\SupplyChargeRepositories;

class SupplyChargeController extends Controller
{
    use SupplyChargeTrait;

    public $supplyChargeRepo;
    public function __construct(SupplyChargeRepositories $supplyChargeRepo)
    {
        $this->supplyChargeRepo = $supplyChargeRepo;
    }
}
