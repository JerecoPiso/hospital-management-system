<?php

namespace App\Http\Controllers;

use App\Traits\SupplyStockTrait;
use App\Repositories\SupplyStockRepositories;

class SupplyStockController extends Controller
{
    use SupplyStockTrait;

    public $supplyStockRepo;
    public function __construct(SupplyStockRepositories $supplyStockRepo)
    {
        $this->supplyStockRepo = $supplyStockRepo;
    }
}
