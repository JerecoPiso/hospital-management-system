<?php

namespace App\Http\Controllers;

use App\Traits\FeeScheduleTrait;
use App\Repositories\FeeScheduleRepositories;

class FeeScheduleController extends Controller
{
    use FeeScheduleTrait;

    public $feeScheduleRepo;
    public function __construct(FeeScheduleRepositories $feeScheduleRepo)
    {
        $this->feeScheduleRepo = $feeScheduleRepo;
    }
}
