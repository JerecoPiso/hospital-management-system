<?php

namespace App\Http\Controllers;

use App\Traits\LabTestTrait;
use App\Repositories\LabTestRepositories;

class LabTestController extends Controller
{
    use LabTestTrait;

    public $labTestRepo;
    public function __construct(LabTestRepositories $labTestRepo)
    {
        $this->labTestRepo = $labTestRepo;
    }
}
