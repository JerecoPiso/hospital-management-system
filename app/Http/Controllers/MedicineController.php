<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\MedicineTrait;
use App\Repositories\MedicineRepositories;

class MedicineController extends Controller
{
    //
    use MedicineTrait;

    public $medicineRepo;
    public function __construct(MedicineRepositories $medicineRepo)
    {
        $this->medicineRepo = $medicineRepo;
    }
}
