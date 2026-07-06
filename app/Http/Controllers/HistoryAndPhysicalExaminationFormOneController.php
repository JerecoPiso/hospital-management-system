<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HistoryAndPhysicalExaminationFormOneTrait;
use App\Repositories\HistoryAndPhysicalExaminationFormOneRepositories;

class HistoryAndPhysicalExaminationFormOneController extends Controller
{
    //
    use HistoryAndPhysicalExaminationFormOneTrait;

    public $historyFormOneRepo;
    public function __construct(HistoryAndPhysicalExaminationFormOneRepositories $historyFormOneRepo)
    {
        $this->historyFormOneRepo = $historyFormOneRepo;
    }
}
