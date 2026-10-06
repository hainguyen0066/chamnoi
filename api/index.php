<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<h1>Fatal Error</h1>";
        echo "<pre>" . print_r($error, true) . "</pre>";
    }
});

try {
    // 1. Tạo thư mục tạm trên Vercel Serverless
    $dirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // 2. Chuyển database.sqlite sang /tmp để SQLite có quyền ĐỌC & GHI trên Vercel
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    $tmpDb = '/tmp/database.sqlite';

    if (!file_exists($tmpDb) && file_exists($sourceDb)) {
        @copy($sourceDb, $tmpDb);
    }

    $dbPath = file_exists($tmpDb) ? $tmpDb : $sourceDb;

    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $dbPath;

    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_ENV['CACHE_STORE'] = 'array';

    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Vercel Deployment Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
