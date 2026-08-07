<?php

namespace App\Http\Controllers;

use App\Traits\DashboardTrait;
use App\Repositories\DashboardRepositories;

class DashboardController extends Controller
{
    use DashboardTrait;

    public $dashboardRepo;
    public function __construct(DashboardRepositories $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }
}
