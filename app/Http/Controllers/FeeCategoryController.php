<?php

namespace App\Http\Controllers;

use App\Traits\FeeCategoryTrait;
use App\Repositories\FeeCategoryRepositories;

class FeeCategoryController extends Controller
{
    use FeeCategoryTrait;

    public $feeCategoryRepo;
    public function __construct(FeeCategoryRepositories $feeCategoryRepo)
    {
        $this->feeCategoryRepo = $feeCategoryRepo;
    }
}
