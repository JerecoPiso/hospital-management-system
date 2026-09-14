<?php

use App\Http\Controllers\SoapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('soaps')
    ->controller(SoapController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:soaps,view');           // list soap notes
        Route::post('/', 'store')->middleware('permission:soaps,create');         // create soap note
        Route::get('/{pid}', 'view')->middleware('permission:soaps,view');      // view soap note
        Route::put('/{pid}', 'update')->middleware('permission:soaps,update');    // update soap note
        Route::delete('/{pid}', 'delete')->middleware('permission:soaps,delete'); // delete soap note
    });
