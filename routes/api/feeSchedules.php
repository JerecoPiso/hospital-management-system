<?php

use App\Http\Controllers\FeeScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('fee-schedules')
    ->controller(FeeScheduleController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:fee-schedules,view');
        Route::post('/', 'store')->middleware('permission:fee-schedules,create');
        Route::get('/{pid}', 'view')->middleware('permission:fee-schedules,view');
        Route::put('/{pid}', 'update')->middleware('permission:fee-schedules,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:fee-schedules,delete');
    });
