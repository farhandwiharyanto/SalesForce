<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/migrate-now-secret', function () {
    try {
        Artisan::call('migrate:fresh', ['--force' => true]);
        Artisan::call('db:seed', ['--class' => 'RoleProfileSeeder', '--force' => true]);
        return "Migration Fresh and seeding successful! Output: " . Artisan::output();
    } catch (\Throwable $e) {
        return "Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . 
               "<br>DB_HOST: " . env('DB_HOST') . 
               "<br>APP_KEY set? " . (env('APP_KEY') ? 'Yes' : 'No');
    }
});
