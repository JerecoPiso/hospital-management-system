<?php

use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('wards')
    ->controller(WardController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list wards
        Route::post('/', 'store');         // create ward
        Route::get('/{pid}', 'view');      // view ward
        Route::put('/{pid}', 'update');    // update ward
        Route::delete('/{pid}', 'delete'); // delete ward
    });
