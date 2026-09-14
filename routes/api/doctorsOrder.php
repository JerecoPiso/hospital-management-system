<?php

use App\Http\Controllers\DoctorsOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('doctors-order')
    ->controller(DoctorsOrderController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:doctors-order,view');          // list orders
        Route::post('/', 'store')->middleware('permission:doctors-order,create');       // create order
        Route::get('/{pid}', 'view')->middleware('permission:doctors-order,view');          // view order
        Route::put('/{pid}', 'update')->middleware('permission:doctors-order,update');    // update order
        Route::delete('/{pid}', 'delete')->middleware('permission:doctors-order,delete');  // delete order
    });
