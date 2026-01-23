<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/', [UserController::class, 'list'])->middleware(["auth:sanctum"]);
    Route::delete('/{pid}', [UserController::class, 'delete'])->middleware(["auth:sanctum"]);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
});
