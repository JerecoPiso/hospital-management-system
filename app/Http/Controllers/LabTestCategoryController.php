<?php

namespace App\Http\Controllers;

use App\Traits\LabTestCategoryTrait;
use App\Repositories\LabTestCategoryRepositories;

class LabTestCategoryController extends Controller
{
    use LabTestCategoryTrait;

    public $labTestCategoryRepo;
    public function __construct(LabTestCategoryRepositories $labTestCategoryRepo)
    {
        $this->labTestCategoryRepo = $labTestCategoryRepo;
    }
}
