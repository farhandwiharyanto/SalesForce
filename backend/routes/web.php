<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/migrate-now-secret', function () {
    // Prevent Laravel from trying to save session to DB after tables are dropped
    config(['session.driver' => 'array']);

    try {
        Artisan::call('migrate:fresh', ['--force' => true]);
        Artisan::call('db:seed', ['--class' => 'RoleProfileSeeder', '--force' => true]);
        return "Migration Fresh and seeding successful! Output: " . Artisan::output();
    } catch (\Throwable $e) {
        return "Error during migration: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . 
               "<br>Output before error: " . Artisan::output();
    }
});
