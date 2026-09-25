<?php

namespace App\Http\Controllers;

use App\Traits\DoctorFeeTrait;
use App\Repositories\DoctorFeeRepositories;

class DoctorFeeController extends Controller
{
    use DoctorFeeTrait;

    public $doctorFeeRepo;
    public function __construct(DoctorFeeRepositories $doctorFeeRepo)
    {
        $this->doctorFeeRepo = $doctorFeeRepo;
    }
}
