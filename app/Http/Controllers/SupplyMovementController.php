<?php

namespace App\Http\Controllers;

use App\Traits\SupplyMovementTrait;
use App\Repositories\SupplyMovementRepositories;

class SupplyMovementController extends Controller
{
    use SupplyMovementTrait;

    public $supplyMovementRepo;
    public function __construct(SupplyMovementRepositories $supplyMovementRepo)
    {
        $this->supplyMovementRepo = $supplyMovementRepo;
    }
}
