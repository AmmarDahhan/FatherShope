<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login'])
    ->middleware([StartSession::class, 'throttle:5,1']);

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout'])
        ->middleware(StartSession::class);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Users (manager only)
    Route::middleware('can:App\Policies\ProductPolicy@manage')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
    });

    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store'])->middleware('can:manage,App\Models\Product');
    Route::put('products/{id}', [ProductController::class, 'update'])->middleware('can:manage,App\Models\Product');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware('can:manage,App\Models\Product');

    // Sales
    Route::post('sales', [SaleController::class, 'store']);
    Route::get('sales', [SaleController::class, 'index']);
    Route::get('sales/{id}', [SaleController::class, 'show']);

    // Dashboard (manager only)
    Route::middleware('can:view-dashboard')->group(function () {
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/inventory-alerts', [DashboardController::class, 'inventoryAlerts']);
    });
});
