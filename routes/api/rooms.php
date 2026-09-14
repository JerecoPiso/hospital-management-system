<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('rooms')
    ->controller(RoomController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:rooms,view');           // list rooms
        Route::post('/', 'store')->middleware('permission:rooms,create');         // create room
        Route::get('/{pid}', 'view')->middleware('permission:rooms,view');      // view room
        Route::put('/{pid}', 'update')->middleware('permission:rooms,update');    // update room
        Route::delete('/{pid}', 'delete')->middleware('permission:rooms,delete'); // delete room
    });
