<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HistoryAndPhysicalExaminationFormTwoTrait;
use App\Repositories\HistoryAndPhysicalExaminationFormTwoRepositories;

class HistoryAndPhysicalExaminationFormTwoController extends Controller
{
    //
    use HistoryAndPhysicalExaminationFormTwoTrait;

    public $historyFormTwoRepo;
    public function __construct(HistoryAndPhysicalExaminationFormTwoRepositories $historyFormTwoRepo)
    {
        $this->historyFormTwoRepo = $historyFormTwoRepo;
    }
}
