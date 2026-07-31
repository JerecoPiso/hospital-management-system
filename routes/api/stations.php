<?php

use App\Http\Controllers\StationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('stations')
    ->controller(StationController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list stations
        Route::post('/', 'store');         // create station
        Route::get('/{pid}', 'view');      // view station
        Route::put('/{pid}', 'update');    // update station
        Route::delete('/{pid}', 'delete'); // delete station
    });
