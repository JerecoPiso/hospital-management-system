<?php

use App\Http\Controllers\MedicineStockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine-stocks')
    ->controller(MedicineStockController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:medicine-stocks,view');           // list medicine stocks
        Route::post('/', 'store')->middleware('permission:medicine-stocks,create');         // create medicine stock
        Route::get('/{pid}', 'view')->middleware('permission:medicine-stocks,view');      // view medicine stock
        Route::put('/{pid}', 'update')->middleware('permission:medicine-stocks,update');    // update medicine stock
        Route::delete('/{pid}', 'delete')->middleware('permission:medicine-stocks,delete'); // delete medicine stock
    });
