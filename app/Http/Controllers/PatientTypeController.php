<?php

namespace App\Http\Controllers;

use App\Traits\PatientTypeTrait;
use App\Repositories\PatientTypeRepositories;

class PatientTypeController extends Controller
{
    use PatientTypeTrait;

    public $patientTypeRepo;
    public function __construct(PatientTypeRepositories $patientTypeRepo)
    {
        $this->patientTypeRepo = $patientTypeRepo;
    }
}
