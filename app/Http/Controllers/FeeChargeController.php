<?php

namespace App\Http\Controllers;

use App\Traits\FeeChargeTrait;
use App\Repositories\FeeChargeRepositories;

class FeeChargeController extends Controller
{
    use FeeChargeTrait;

    public $feeChargeRepo;
    public function __construct(FeeChargeRepositories $feeChargeRepo)
    {
        $this->feeChargeRepo = $feeChargeRepo;
    }
}
