<?php

namespace App\Http\Controllers;

use App\Traits\SoapTrait;
use App\Repositories\SoapRepositories;
use App\Repositories\PatientCaseRepositories;

class SoapController extends Controller
{
    use SoapTrait;

    public $soapRepo;
    public $patientCaseRepo;

    public function __construct(SoapRepositories $soapRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->soapRepo = $soapRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
