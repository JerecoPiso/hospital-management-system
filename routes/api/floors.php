<?php

use App\Http\Controllers\FloorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('floors')
    ->controller(FloorController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list floors
        Route::post('/', 'store');         // create floor
        Route::get('/{pid}', 'view');      // view floor
        Route::put('/{pid}', 'update');    // update floor
        Route::delete('/{pid}', 'delete'); // delete floor
    });
