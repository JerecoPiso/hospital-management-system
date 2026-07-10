<?php

namespace App\Http\Controllers;

use App\Traits\PatientTrait;
use App\Repositories\PatientRepositories;;

class PatientController extends Controller
{
    //
    use PatientTrait;

    public $patientRepo;
    public function __construct(PatientRepositories $patientRepo)
    {
        $this->patientRepo = $patientRepo;
    }
}
