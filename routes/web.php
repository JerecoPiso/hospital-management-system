<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;





Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});


Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');
