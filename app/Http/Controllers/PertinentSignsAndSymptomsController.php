<?php

namespace App\Http\Controllers;

use App\Traits\PertinentSignsAndSymptomsTrait;
use App\Repositories\PertinentSignsAndSymptomsRepositories;
use App\Repositories\PatientCaseRepositories;

class PertinentSignsAndSymptomsController extends Controller
{
    use PertinentSignsAndSymptomsTrait;

    public $pertinentRepo;
    public $patientCaseRepo;

    public function __construct(PertinentSignsAndSymptomsRepositories $pertinentRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->pertinentRepo = $pertinentRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
