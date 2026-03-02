<?php

use App\Http\Controllers\DoctorsOrderController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::post('/register', [UserController::class, 'register']);
// Route::post('/login', [UserController::class, 'login']);

Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/', 'list')->middleware(["auth:sanctum"]);
    Route::delete('/{pid}', 'delete')->middleware(["auth:sanctum"]);
    Route::post('/logout', 'logout')->middleware(["auth:sanctum"]);

    // Route::post('/login', 'login');
    Route::post('/register', 'register');
});



Route::middleware(['auth:sanctum', 'auth.session'])->prefix('doctors-order')

    ->controller(DoctorsOrderController::class)
    ->group(function (): void {

        Route::get('/', 'list');          // list orders
        Route::post('/', 'store');         // create order
        // Route::delete('/{pid}', 'destroy'); // delete order
    });

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
