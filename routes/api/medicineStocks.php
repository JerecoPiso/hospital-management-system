<?php

use App\Http\Controllers\MedicineStockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine-stocks')
    ->controller(MedicineStockController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list medicine stocks
        Route::post('/', 'store');         // create medicine stock
        Route::get('/{pid}', 'view');      // view medicine stock
        Route::put('/{pid}', 'update');    // update medicine stock
        Route::delete('/{pid}', 'delete'); // delete medicine stock
    });
