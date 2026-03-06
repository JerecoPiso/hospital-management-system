<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::post('/register', [UserController::class, 'register'])->middleware(["auth:sanctum"]);
    Route::get('/list', 'list');
    Route::post('/logout', 'logout')->middleware(["auth:sanctum"]);
    Route::put('/{pid}', 'update');    // update note
    Route::get('/{pid}', 'view');          // view note
    Route::delete('/{pid}', 'delete')->middleware(["auth:sanctum"]);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
