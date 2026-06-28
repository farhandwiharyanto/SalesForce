<?php
// Vercel serverless functions have a read-only filesystem.
// We must override the storage path to /tmp where it's writable.
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0777, true);
    mkdir($storagePath . '/framework/cache/data', 0777, true);
    mkdir($storagePath . '/framework/views', 0777, true);
    mkdir($storagePath . '/framework/sessions', 0777, true);
    mkdir($storagePath . '/logs', 0777, true);
}
$_ENV['APP_STORAGE'] = $storagePath;
putenv("APP_STORAGE={$storagePath}");

$_ENV['APP_DEBUG'] = 'true';
putenv("APP_DEBUG=true");

// Laravel requires these paths to be writable for caching and logging
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/framework/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/framework/cache/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/framework/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/framework/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/framework/cache/events.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv("APP_SERVICES_CACHE={$_ENV['APP_SERVICES_CACHE']}");
putenv("APP_PACKAGES_CACHE={$_ENV['APP_PACKAGES_CACHE']}");
putenv("APP_CONFIG_CACHE={$_ENV['APP_CONFIG_CACHE']}");
putenv("APP_ROUTES_CACHE={$_ENV['APP_ROUTES_CACHE']}");
putenv("APP_EVENTS_CACHE={$_ENV['APP_EVENTS_CACHE']}");
putenv("VIEW_COMPILED_PATH={$_ENV['VIEW_COMPILED_PATH']}");

// Vercel handles stderr well for logs, avoiding file write issues
if (!isset($_ENV['LOG_CHANNEL'])) {
    $_ENV['LOG_CHANNEL'] = 'stderr';
    putenv("LOG_CHANNEL=stderr");
}

require __DIR__ . '/../public/index.php';
