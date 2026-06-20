<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DealController;

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// Endpoint khusus Integrasi OrderSales (Bisa diamankan dengan API Key / Client Credentials nanti)
Route::get('/integration/ordersales/customers', [CustomerController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('leads', LeadController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('customers', CustomerController::class);
    
    // Deals discount endpoints
    Route::post('/deals/{deal}/discount-request', [DealController::class, 'requestDiscount']);
    Route::post('/deals/{deal}/discount-approve', [DealController::class, 'approveDiscount']);
    Route::post('/deals/{deal}/discount-reject', [DealController::class, 'rejectDiscount']);
    
    Route::apiResource('deals', DealController::class);
});
