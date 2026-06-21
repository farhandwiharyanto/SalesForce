<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    // Vercel deployment fix: Force SCRIPT_NAME to /index.php so Laravel doesn't strip the /api prefix
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_ENV['APP_DEBUG'] = 'true';
    putenv('APP_DEBUG=true');
    
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
}
