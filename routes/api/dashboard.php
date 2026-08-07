<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('dashboard')
    ->controller(DashboardController::class)
    ->group(function (): void {
        Route::get('/summary', 'summary');
    });
