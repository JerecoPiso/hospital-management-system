<?php

use App\Http\Controllers\SupplyDistributionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-distributions')
    ->controller(SupplyDistributionController::class)
    ->group(function (): void {
        Route::get('/', 'list');      // list supply distributions
        Route::post('/', 'store');    // create supply distribution (adjusts stock quantity)
        Route::get('/{pid}', 'view'); // view supply distribution
    });
