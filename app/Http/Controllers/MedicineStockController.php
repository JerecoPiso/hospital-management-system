<?php

namespace App\Http\Controllers;

use App\Traits\MedicineStockTrait;
use App\Repositories\MedicineStockRepositories;

class MedicineStockController extends Controller
{
    use MedicineStockTrait;

    public $medicineStockRepo;
    public function __construct(MedicineStockRepositories $medicineStockRepo)
    {
        $this->medicineStockRepo = $medicineStockRepo;
    }
}
