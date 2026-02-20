<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Customer routes use the 'web' guard (customers provider).
| Admin routes use the 'admin' guard (users provider).
| Both use Sanctum tokens for API authentication.
|
*/

// ==========================================================================
// Customer Auth (public)
// ==========================================================================
Route::prefix('customer')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);

    // Protected customer routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/profile', [CustomerAuthController::class, 'profile']);
    });
});

// ==========================================================================
// Admin Auth (public)
// ==========================================================================
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Protected admin routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/profile', [AdminAuthController::class, 'profile']);
    });
});
