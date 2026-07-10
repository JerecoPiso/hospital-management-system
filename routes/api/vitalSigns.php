<?php

use App\Http\Controllers\VitalSignController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('vital-signs')
    ->controller(VitalSignController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list vital signs
        Route::post('/', 'store');         // create vital sign
        Route::get('/{pid}', 'view');      // view vital sign
        Route::put('/{pid}', 'update');    // update vital sign
        Route::delete('/{pid}', 'delete'); // delete vital sign
    });
