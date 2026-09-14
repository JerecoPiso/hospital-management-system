<?php

use App\Http\Controllers\StationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('stations')
    ->controller(StationController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:stations,view');           // list stations
        Route::post('/', 'store')->middleware('permission:stations,create');         // create station
        Route::get('/{pid}', 'view')->middleware('permission:stations,view');      // view station
        Route::put('/{pid}', 'update')->middleware('permission:stations,update');    // update station
        Route::delete('/{pid}', 'delete')->middleware('permission:stations,delete'); // delete station
    });
