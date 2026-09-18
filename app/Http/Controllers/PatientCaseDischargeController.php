<?php

namespace App\Http\Controllers;

use App\Traits\PatientCaseDischargeTrait;
use  App\Repositories\PatientCaseDischargeRepositories;
use App\Repositories\PatientCaseRepositories;

class PatientCaseDischargeController extends Controller
{
    //
    use PatientCaseDischargeTrait;
    public $patientCaseDischargeRepo;
    public $patientCaseRepo;

    public function __construct(PatientCaseDischargeRepositories $patientCaseDischargeRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->patientCaseDischargeRepo = $patientCaseDischargeRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
