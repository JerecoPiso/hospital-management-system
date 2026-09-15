<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::post('/register', 'register')->middleware(["auth:sanctum", "permission:users,create"]);
    Route::get('/list', 'list')->middleware(["auth:sanctum", "permission:users,view"]);
    Route::post('/logout', 'logout')->middleware(["auth:sanctum"]);
    Route::put('/change-password', 'changePassword')->middleware(["auth:sanctum"]);
    Route::put('/{pid}', 'update')->middleware(["auth:sanctum", "permission:users,update"]);
    Route::get('/{pid}', 'view')->middleware(["auth:sanctum", "permission:users,view"]);
    Route::delete('/{pid}', 'delete')->middleware(["auth:sanctum", "permission:users,delete"]);
});
Route::get('/user', [UserController::class, 'me'])->middleware('auth:sanctum');
