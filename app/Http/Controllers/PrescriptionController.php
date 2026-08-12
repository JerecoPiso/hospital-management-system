<?php

namespace App\Http\Controllers;

use App\Traits\PrescriptionTrait;
use App\Repositories\PrescriptionRepositories;

class PrescriptionController extends Controller
{
    use PrescriptionTrait;
    public $prescriptionRepo;
    public function __construct(PrescriptionRepositories $prescriptionRepo)
    {
        $this->prescriptionRepo = $prescriptionRepo;
    }
}
