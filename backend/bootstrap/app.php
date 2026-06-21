<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ], 500);
            }
        });
    })->create();

if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL_URL'])) {
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        @mkdir($storagePath, 0777, true);
        @mkdir($storagePath . '/framework/cache/data', 0777, true);
        @mkdir($storagePath . '/framework/views', 0777, true);
        @mkdir($storagePath . '/framework/sessions', 0777, true);
        @mkdir($storagePath . '/logs', 0777, true);
    }
    $app->useStoragePath($storagePath);

    // Override cache paths to /tmp
    $_SERVER['APP_SERVICES_CACHE'] = '/tmp/storage/framework/cache/services.php';
    $_SERVER['APP_PACKAGES_CACHE'] = '/tmp/storage/framework/cache/packages.php';
    $_SERVER['APP_CONFIG_CACHE'] = '/tmp/storage/framework/cache/config.php';
    $_SERVER['APP_ROUTES_CACHE'] = '/tmp/storage/framework/cache/routes.php';
    $_SERVER['APP_EVENTS_CACHE'] = '/tmp/storage/framework/cache/events.php';
    $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
    $_SERVER['LOG_CHANNEL'] = 'stderr';

    foreach (['APP_SERVICES_CACHE', 'APP_PACKAGES_CACHE', 'APP_CONFIG_CACHE', 'APP_ROUTES_CACHE', 'APP_EVENTS_CACHE', 'VIEW_COMPILED_PATH', 'LOG_CHANNEL'] as $key) {
        $_ENV[$key] = $_SERVER[$key];
        putenv("{$key}={$_SERVER[$key]}");
    }
}

return $app;
