<?php

use App\Http\Controllers\MedicineStockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine-stock-movements')
    ->controller(MedicineStockMovementController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:medicine-stock-movements,view');      // list medicine stock movements
        Route::post('/', 'store')->middleware('permission:medicine-stock-movements,create');    // create medicine stock movement (audit log entry)
        Route::get('/{pid}', 'view')->middleware('permission:medicine-stock-movements,view'); // view medicine stock movement
    });
