<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('invoices')
    ->controller(InvoiceController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:invoices,view');
        Route::post('/generate', 'generate')->middleware('permission:invoices,create');
        Route::get('/charges', 'charges')->middleware('permission:invoices,view');
        Route::get('/{pid}', 'view')->middleware('permission:invoices,view');
        Route::post('/{pid}/payments', 'pay')->middleware('permission:invoices,update');
    });
