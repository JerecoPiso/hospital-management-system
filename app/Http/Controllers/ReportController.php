<?php

namespace App\Http\Controllers;

use App\Traits\ReportTrait;
use App\Repositories\ReportRepositories;

class ReportController extends Controller
{
    use ReportTrait;

    public $reportRepo;
    public function __construct(ReportRepositories $reportRepo)
    {
        $this->reportRepo = $reportRepo;
    }
}
