<?php

namespace App\Http\Controllers;

use App\Traits\VitalSignTrait;
use App\Repositories\VitalSignRepositories;
use App\Repositories\PatientCaseRepositories;

class VitalSignController extends Controller
{
    //
    use VitalSignTrait;
    public $vitalSignRepo;
    public $patientCaseRepo;
    public function __construct(VitalSignRepositories $vitalSignRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->vitalSignRepo = $vitalSignRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
