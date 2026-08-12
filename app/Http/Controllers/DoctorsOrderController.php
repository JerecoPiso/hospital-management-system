<?php

namespace App\Http\Controllers;

use App\Traits\DoctorsOrderTrait;
use App\Repositories\DoctorsOrderRepositories;
use App\Repositories\PatientCaseRepositories;
class DoctorsOrderController extends Controller
{
    //
    use DoctorsOrderTrait;
    public $doctorsOrderRepo;
    public $patientCaseRepo;
    public function __construct(DoctorsOrderRepositories $doctorsOrderRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->doctorsOrderRepo = $doctorsOrderRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
