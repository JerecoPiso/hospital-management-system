<?php

use App\Http\Controllers\MedicineDistributionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine-distributions')
    ->controller(MedicineDistributionController::class)
    ->group(function (): void {
        Route::get('/', 'list');      // list medicine distributions
        Route::post('/', 'store');    // create medicine distribution (adjusts stock quantity)
        Route::get('/{pid}', 'view'); // view medicine distribution
    });
