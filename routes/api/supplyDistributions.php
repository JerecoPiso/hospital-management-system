<?php

use App\Http\Controllers\SupplyDistributionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-distributions')
    ->controller(SupplyDistributionController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:supply-distributions,view');      // list supply distributions
        Route::post('/', 'store')->middleware('permission:supply-distributions,create');    // create supply distribution (adjusts stock quantity)
        Route::get('/{pid}', 'view')->middleware('permission:supply-distributions,view'); // view supply distribution
    });
