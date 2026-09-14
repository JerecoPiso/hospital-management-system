<?php

use App\Http\Controllers\MedicineDistributionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine-distributions')
    ->controller(MedicineDistributionController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:medicine-distributions,view');      // list medicine distributions
        Route::post('/', 'store')->middleware('permission:medicine-distributions,create');    // create medicine distribution (adjusts stock quantity)
        Route::get('/{pid}', 'view')->middleware('permission:medicine-distributions,view'); // view medicine distribution
    });
