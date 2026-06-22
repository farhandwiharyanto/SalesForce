<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OptyController;

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

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
    Route::apiResource('users', UserController::class);
    Route::apiResource('sia-contracts', \App\Http\Controllers\Api\SiaContractController::class)->only(['index', 'store']);
    Route::apiResource('webhook-logs', \App\Http\Controllers\Api\WebhookLogController::class)->only(['index']);
    
    // Optys discount endpoints
    Route::post('/optys/{opty}/discount-request', [OptyController::class, 'requestDiscount']);
    Route::post('/optys/{opty}/discount-approve', [OptyController::class, 'approveDiscount']);
    Route::post('/optys/{opty}/discount-reject', [OptyController::class, 'rejectDiscount']);
    Route::post('/webhook-logs/{webhookLog}/retry', [\App\Http\Controllers\Api\WebhookLogController::class, 'retry']);
    
    Route::apiResource('optys', OptyController::class);
});
