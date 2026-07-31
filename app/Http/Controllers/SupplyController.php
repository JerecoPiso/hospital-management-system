<?php

namespace App\Http\Controllers;

use App\Traits\SupplyTrait;
use App\Repositories\SupplyRepositories;

class SupplyController extends Controller
{
    use SupplyTrait;

    public $supplyRepo;
    public function __construct(SupplyRepositories $supplyRepo)
    {
        $this->supplyRepo = $supplyRepo;
    }
}
