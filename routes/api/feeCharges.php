<?php

use App\Http\Controllers\FeeChargeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('fee-charges')
    ->controller(FeeChargeController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:fee-charges,view');
        Route::post('/', 'store')->middleware('permission:fee-charges,create');
        Route::get('/{pid}', 'view')->middleware('permission:fee-charges,view');
        Route::delete('/{pid}', 'delete')->middleware('permission:fee-charges,delete');
    });
