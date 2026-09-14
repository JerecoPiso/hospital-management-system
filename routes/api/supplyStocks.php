<?php

use App\Http\Controllers\SupplyStockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-stocks')
    ->controller(SupplyStockController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:supply-stocks,view');           // list supply stocks
        Route::post('/', 'store')->middleware('permission:supply-stocks,create');         // create supply stock
        Route::get('/{pid}', 'view')->middleware('permission:supply-stocks,view');      // view supply stock
        Route::put('/{pid}', 'update')->middleware('permission:supply-stocks,update');    // update supply stock
        Route::delete('/{pid}', 'delete')->middleware('permission:supply-stocks,delete'); // delete supply stock
    });
