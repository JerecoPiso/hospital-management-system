<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('reports')
    ->controller(ReportController::class)
    ->group(function (): void {
        Route::get('/dispense-medicine-stocks', 'dispenseMedicineStocks')->middleware('permission:dispense-medicine-stocks-report,view');
        Route::get('/patient-invoices', 'patientInvoice')->middleware('permission:patient-invoice-report,view');
    });
