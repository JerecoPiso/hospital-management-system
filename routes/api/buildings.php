<?php

use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('buildings')
    ->controller(BuildingController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list buildings
        Route::post('/', 'store');         // create building
        Route::get('/{pid}', 'view');      // view building
        Route::put('/{pid}', 'update');    // update building
        Route::delete('/{pid}', 'delete'); // delete building
    });
