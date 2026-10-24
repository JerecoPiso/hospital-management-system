<?php

namespace App\Http\Controllers;

use App\Traits\ExaminationOptionTrait;
use App\Repositories\ExaminationOptionRepositories;

class ExaminationOptionController extends Controller
{
    use ExaminationOptionTrait;

    public $examinationOptionRepo;

    public function __construct(ExaminationOptionRepositories $examinationOptionRepo)
    {
        $this->examinationOptionRepo = $examinationOptionRepo;
    }
}
