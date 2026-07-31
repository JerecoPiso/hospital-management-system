<?php

use App\Http\Controllers\SupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supplies')
    ->controller(SupplyController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list supplies
        Route::post('/', 'store');         // create supply
        Route::get('/{pid}', 'view');      // view supply
        Route::put('/{pid}', 'update');    // update supply
        Route::delete('/{pid}', 'delete'); // delete supply
    });
