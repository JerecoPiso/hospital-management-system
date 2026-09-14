<?php

use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('buildings')
    ->controller(BuildingController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:buildings,view');           // list buildings
        Route::post('/', 'store')->middleware('permission:buildings,create');       // create building
        Route::get('/{pid}', 'view')->middleware('permission:buildings,view');      // view building
        Route::put('/{pid}', 'update')->middleware('permission:buildings,update');  // update building
        Route::delete('/{pid}', 'delete')->middleware('permission:buildings,delete'); // delete building
    });
