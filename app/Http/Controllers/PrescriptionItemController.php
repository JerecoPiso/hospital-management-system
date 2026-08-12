<?php

namespace App\Http\Controllers;

use App\Traits\PrescriptionItemTrait;
use App\Repositories\PrescriptionItemRepositories;

class PrescriptionItemController extends Controller
{
    use PrescriptionItemTrait;
    public $prescriptionItemRepo;
    public function __construct(PrescriptionItemRepositories $prescriptionItemRepo)
    {
        $this->prescriptionItemRepo = $prescriptionItemRepo;
    }
}
