<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\NursesNotesTrait;
use App\Repositories\NursesNotesRepositories;
use App\Repositories\PatientCaseRepositories;

class NursesNoteController extends Controller
{
    //
    use NursesNotesTrait;

    public $nurseNotesRepo;
    public $patientCaseRepo;
    public function __construct(NursesNotesRepositories $nurseNotesRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->nurseNotesRepo = $nurseNotesRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
