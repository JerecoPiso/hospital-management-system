<?php

namespace App\Http\Controllers;

use App\Traits\MedicineStockMovementTrait;
use App\Repositories\MedicineStockMovementRepositories;

class MedicineStockMovementController extends Controller
{
    use MedicineStockMovementTrait;

    public $medicineStockMovementRepo;
    public function __construct(MedicineStockMovementRepositories $medicineStockMovementRepo)
    {
        $this->medicineStockMovementRepo = $medicineStockMovementRepo;
    }
}
