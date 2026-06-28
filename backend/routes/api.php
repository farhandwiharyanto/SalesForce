<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OptyController;
use App\Http\Controllers\Api\ActivityController;

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;

Route::get('/migrate-now-secret', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RoleProfileSeeder', '--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
        return "Migration Fresh and seeding successful! Output: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        return "Error during migration: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . 
               "<br>Output before error: " . \Illuminate\Support\Facades\Artisan::output();
    }
});

Route::post('/login', [AuthController::class, 'login']);

// Endpoint khusus Integrasi OrderSales (Bisa diamankan dengan API Key / Client Credentials nanti)
Route::get('/integration/ordersales/customers', [CustomerController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('leads/bulk', [LeadController::class, 'bulkStore']);
    Route::post('optys/bulk', [\App\Http\Controllers\Api\OptyController::class, 'bulkStore']);
    Route::post('customers/bulk', [CustomerController::class, 'bulkStore']);
    Route::post('products/bulk', [ProductController::class, 'bulkStore']);
    Route::post('contracts/bulk', [\App\Http\Controllers\Api\ContractController::class, 'bulkStore']);
    Route::post('service-instance-accounts/bulk', [\App\Http\Controllers\Api\ServiceInstanceAccountController::class, 'bulkStore']);

    Route::apiResource('leads', LeadController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('contracts', \App\Http\Controllers\Api\ContractController::class);
    Route::apiResource('service-instance-accounts', \App\Http\Controllers\Api\ServiceInstanceAccountController::class)->only(['index', 'store', 'update', 'show']);
    Route::apiResource('webhook-logs', \App\Http\Controllers\Api\WebhookLogController::class)->only(['index']);
    
    // Opty Routes
    Route::post('/optys/bulk', [OptyController::class, 'bulkStore']);
    Route::post('/optys/{opty}/discount/request', [OptyController::class, 'requestDiscount']);
    Route::post('/optys/{opty}/discount/approve', [OptyController::class, 'approveDiscount']);
    Route::post('/optys/{opty}/discount/reject', [OptyController::class, 'rejectDiscount']);
    Route::get('/optys/{opty}/histories', [OptyController::class, 'histories']);
    Route::post('/optys/{opty}/upload-contract', [OptyController::class, 'uploadContract']);
    Route::apiResource('optys', OptyController::class);

    // Opty Approvals / Inbox
    Route::get('/opty-approvals/inbox', [\App\Http\Controllers\Api\OptyApprovalController::class, 'inbox']);
    Route::post('/optys/{opty}/submit-approval', [\App\Http\Controllers\Api\OptyApprovalController::class, 'submitApproval']);
    
    Route::post('/webhook-logs/{webhookLog}/retry', [\App\Http\Controllers\Api\WebhookLogController::class, 'retry']);
    
    Route::get('/audit-logs/{type}/{id}', [\App\Http\Controllers\Api\AuditLogController::class, 'index']);
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/login-histories', [\App\Http\Controllers\Api\LoginHistoryController::class, 'index']);
    
    Route::get('/roles/{role}/privileges', [RoleController::class, 'getPrivileges']);
    Route::put('/roles/{role}/privileges', [RoleController::class, 'updatePrivileges']);
    Route::put('/roles/{role}/remove-from-users', [RoleController::class, 'removeFromUsers']);
    
    Route::apiResource('role-profiles', \App\Http\Controllers\Api\RoleProfileController::class);
    
    Route::apiResource('optys', OptyController::class);
});
