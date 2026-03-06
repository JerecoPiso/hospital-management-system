<?php

use App\Http\Controllers\DoctorsOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('doctors-order')
    ->controller(DoctorsOrderController::class)
    ->group(function (): void {
        Route::get('/', 'list');          // list orders
        Route::post('/', 'store');         // create order
        Route::get('/{pid}', 'view');          // view order
        Route::put('/{pid}', 'update');    // update order
        Route::delete('/{pid}', 'delete');  // delete order
    });
