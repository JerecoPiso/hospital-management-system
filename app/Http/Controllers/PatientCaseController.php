<?php

namespace App\Http\Controllers;

use App\Traits\PatientCaseTrait;
use App\Repositories\PatientCaseRepositories;

class PatientCaseController extends Controller
{
    use PatientCaseTrait;

    public $patientCaseRepo;
    public function __construct(PatientCaseRepositories $patientCaseRepo)
    {
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
