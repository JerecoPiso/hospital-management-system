<?php

use App\Http\Controllers\BedController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('beds')
    ->controller(BedController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:beds,view');           // list beds
        Route::post('/', 'store')->middleware('permission:beds,create');       // create bed
        Route::get('/{pid}', 'view')->middleware('permission:beds,view');      // view bed
        Route::put('/{pid}', 'update')->middleware('permission:beds,update');  // update bed
        Route::delete('/{pid}', 'delete')->middleware('permission:beds,delete'); // delete bed
    });
