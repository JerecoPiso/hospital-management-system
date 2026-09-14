<?php

use App\Http\Controllers\SupplyMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-movements')
    ->controller(SupplyMovementController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:supply-movements,view');      // list supply movements
        Route::post('/', 'store')->middleware('permission:supply-movements,create');    // create supply movement (adjusts stock quantity)
        Route::get('/{pid}', 'view')->middleware('permission:supply-movements,view'); // view supply movement
    });
