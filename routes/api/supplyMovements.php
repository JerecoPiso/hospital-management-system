<?php

use App\Http\Controllers\SupplyMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-movements')
    ->controller(SupplyMovementController::class)
    ->group(function (): void {
        Route::get('/', 'list');      // list supply movements
        Route::post('/', 'store');    // create supply movement (adjusts stock quantity)
        Route::get('/{pid}', 'view'); // view supply movement
    });
