<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HistoryAndPhysicalExaminationFormTwoTrait;
use App\Repositories\HistoryAndPhysicalExaminationFormTwoRepositories;
use App\Repositories\PatientCaseRepositories;

class HistoryAndPhysicalExaminationFormTwoController extends Controller
{
    //
    use HistoryAndPhysicalExaminationFormTwoTrait;

    public $historyFormTwoRepo;
    public $patientCaseRepo;
    public function __construct(HistoryAndPhysicalExaminationFormTwoRepositories $historyFormTwoRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->historyFormTwoRepo = $historyFormTwoRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
