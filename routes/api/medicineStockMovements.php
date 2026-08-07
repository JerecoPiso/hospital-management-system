<?php

use App\Http\Controllers\MedicineStockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine-stock-movements')
    ->controller(MedicineStockMovementController::class)
    ->group(function (): void {
        Route::get('/', 'list');      // list medicine stock movements
        Route::post('/', 'store');    // create medicine stock movement (audit log entry)
        Route::get('/{pid}', 'view'); // view medicine stock movement
    });
