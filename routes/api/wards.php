<?php

use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('wards')
    ->controller(WardController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:wards,view');           // list wards
        Route::post('/', 'store')->middleware('permission:wards,create');         // create ward
        Route::get('/{pid}', 'view')->middleware('permission:wards,view');      // view ward
        Route::put('/{pid}', 'update')->middleware('permission:wards,update');    // update ward
        Route::delete('/{pid}', 'delete')->middleware('permission:wards,delete'); // delete ward
    });
