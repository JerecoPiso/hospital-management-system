<?php

use App\Http\Controllers\BedController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('beds')
    ->controller(BedController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list beds
        Route::post('/', 'store');         // create bed
        Route::get('/{pid}', 'view');      // view bed
        Route::put('/{pid}', 'update');    // update bed
        Route::delete('/{pid}', 'delete'); // delete bed
    });
