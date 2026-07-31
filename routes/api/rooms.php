<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('rooms')
    ->controller(RoomController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list rooms
        Route::post('/', 'store');         // create room
        Route::get('/{pid}', 'view');      // view room
        Route::put('/{pid}', 'update');    // update room
        Route::delete('/{pid}', 'delete'); // delete room
    });
