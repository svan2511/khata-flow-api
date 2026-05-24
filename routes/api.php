<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/shop/setup', [ShopController::class, 'setup']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

    Route::get('/sync/status', [SyncController::class, 'status']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::post('/products/quick', [ProductController::class, 'quickAdd']);
    Route::get('/products/{uuid}', [ProductController::class, 'show']);
    Route::put('/products/{uuid}', [ProductController::class, 'update']);
    Route::delete('/products/{uuid}', [ProductController::class, 'destroy']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/search', [CustomerController::class, 'search']);
    Route::post('/customers/quick', [CustomerController::class, 'quickAdd']);
    Route::get('/customers/{uuid}', [CustomerController::class, 'show']);
    Route::put('/customers/{uuid}', [CustomerController::class, 'update']);

    // Bills
    Route::post('/bills', [BillController::class, 'store']);
    Route::get('/bills', [BillController::class, 'index']);
    Route::get('/bills/{uuid}', [BillController::class, 'show']);
    Route::post('/bills/{uuid}/payment', [BillController::class, 'addPayment']);

    // Stock
    Route::post('/stock/in', [StockController::class, 'stockIn']);

    // Reports
    Route::get('/reports/daily', [ReportController::class, 'daily']);
    Route::get('/reports/monthly', [ReportController::class, 'monthly']);

    // Expenses
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
});
