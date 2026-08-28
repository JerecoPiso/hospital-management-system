<?php

namespace App\Http\Controllers;

use App\Traits\PatientCaseDietTrait;
use App\Repositories\PatientCaseDietRepositories;

class PatientCaseDietController extends Controller
{
    use PatientCaseDietTrait;

    public $patientCaseDietRepo;

    public function __construct(PatientCaseDietRepositories $patientCaseDietRepo)
    {
        $this->patientCaseDietRepo = $patientCaseDietRepo;
    }
}
