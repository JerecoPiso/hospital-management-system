<?php

namespace App\Http\Controllers;

use App\Traits\InvoiceTrait;
use App\Repositories\InvoiceRepositories;

class InvoiceController extends Controller
{
    use InvoiceTrait;

    public $invoiceRepo;
    public function __construct(InvoiceRepositories $invoiceRepo)
    {
        $this->invoiceRepo = $invoiceRepo;
    }
}
