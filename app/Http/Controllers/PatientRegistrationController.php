<?php

namespace App\Http\Controllers;

use App\Traits\PatientRegistrationTrait;
use App\Repositories\PatientRegistrationRepositories;

class PatientRegistrationController extends Controller
{
    //
    use PatientRegistrationTrait;

    public $patientRegistrationRepo;
    public function __construct(PatientRegistrationRepositories $patientRegistrationRepo)
    {
        $this->patientRegistrationRepo = $patientRegistrationRepo;
    }
}
