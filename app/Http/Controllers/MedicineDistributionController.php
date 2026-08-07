<?php

namespace App\Http\Controllers;

use App\Traits\MedicineDistributionTrait;
use App\Repositories\MedicineDistributionRepositories;

class MedicineDistributionController extends Controller
{
    use MedicineDistributionTrait;

    public $medicineDistributionRepo;
    public function __construct(MedicineDistributionRepositories $medicineDistributionRepo)
    {
        $this->medicineDistributionRepo = $medicineDistributionRepo;
    }
}
