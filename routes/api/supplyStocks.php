<?php

use App\Http\Controllers\SupplyStockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-stocks')
    ->controller(SupplyStockController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list supply stocks
        Route::post('/', 'store');         // create supply stock
        Route::get('/{pid}', 'view');      // view supply stock
        Route::put('/{pid}', 'update');    // update supply stock
        Route::delete('/{pid}', 'delete'); // delete supply stock
    });
