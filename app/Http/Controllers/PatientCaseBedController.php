<?php

namespace App\Http\Controllers;

use App\Traits\PatientCaseBedTrait;
use  App\Repositories\PatientCaseBedRepositories;
use App\Repositories\PatientCaseRepositories;

class PatientCaseBedController extends Controller
{
    //
    use PatientCaseBedTrait;
    public $patientCaseBedRepo;
    public $patientCaseRepo;

    public function __construct(PatientCaseBedRepositories $patientCaseBedRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->patientCaseBedRepo = $patientCaseBedRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
