<?php

use App\Http\Controllers\FloorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('floors')
    ->controller(FloorController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:floors,view');           // list floors
        Route::post('/', 'store')->middleware('permission:floors,create');       // create floor
        Route::get('/{pid}', 'view')->middleware('permission:floors,view');      // view floor
        Route::put('/{pid}', 'update')->middleware('permission:floors,update');  // update floor
        Route::delete('/{pid}', 'delete')->middleware('permission:floors,delete'); // delete floor
    });
