<?php

use App\Http\Controllers\SoapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('soaps')
    ->controller(SoapController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list soap notes
        Route::post('/', 'store');         // create soap note
        Route::get('/{pid}', 'view');      // view soap note
        Route::put('/{pid}', 'update');    // update soap note
        Route::delete('/{pid}', 'delete'); // delete soap note
    });
