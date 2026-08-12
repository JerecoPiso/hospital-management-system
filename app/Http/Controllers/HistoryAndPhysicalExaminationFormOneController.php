<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HistoryAndPhysicalExaminationFormOneTrait;
use App\Repositories\HistoryAndPhysicalExaminationFormOneRepositories;
use App\Repositories\PatientCaseRepositories;
class HistoryAndPhysicalExaminationFormOneController extends Controller
{
    //
    use HistoryAndPhysicalExaminationFormOneTrait;

    public $historyFormOneRepo;
    public $patientCaseRepo;
    public function __construct(HistoryAndPhysicalExaminationFormOneRepositories $historyFormOneRepo, PatientCaseRepositories $patientCaseRepo)
    {
        $this->historyFormOneRepo = $historyFormOneRepo;
        $this->patientCaseRepo = $patientCaseRepo;
    }
}
