<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderDetailController;
use App\Http\Controllers\Api\PayeeController;
use App\Http\Controllers\Api\PayeeTransController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\BankTransController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\TaxRateController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProfileController;

Route::post('signup', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('test-api', function() {
    return 'API is working';
});

Route::middleware('auth:sanctum')->group(function () {
    // Products - Manager can manage, everyone can view
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
        Route::post('products/{id}/images', [ProductController::class, 'uploadImages']);
    });
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('products/barcode/lookup', [ProductController::class, 'findByBarcode']);

    // Orders - Cashier can create, Manager can void
    Route::middleware(['permission:create_orders'])->group(function () {
        Route::post('orders', [OrderController::class, 'store']);
        Route::put('orders/{id}', [OrderController::class, 'update']); // Pending updates
    });
    Route::middleware(['permission:void_orders'])->post('orders/{id}/void', [OrderController::class, 'void']);
    
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']); // Soft delete
    Route::get('orders/pdf/{id}', [OrderController::class, 'downloadPdf']);

    // Order Details
    Route::apiResource('order-details', OrderDetailController::class)->except(['store', 'update', 'destroy']);

    // Customers
    Route::apiResource('customers', CustomerController::class);

    // Tax Rates - Manager only
    Route::middleware(['permission:manage_tax_rates'])->apiResource('tax-rates', TaxRateController::class);

    // Payees - Manager only
    Route::middleware(['permission:manage_payees'])->group(function () {
        Route::apiResource('payees', PayeeController::class);
        Route::apiResource('payee-trans', PayeeTransController::class);
    });

    // Banks - Manager only
    Route::middleware(['permission:manage_banks'])->group(function () {
        Route::apiResource('banks', BankController::class);
        Route::apiResource('bank-trans', BankTransController::class);
    });

    // Auth & Profile
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('company', [CompanyController::class, 'show']);
    Route::put('company', [CompanyController::class, 'update']);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'changePassword']);
});
