<?php

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

$sourceDb = __DIR__ . '/../database/database.sqlite';
$tmpDb = '/tmp/database.sqlite';

if (!file_exists($tmpDb) && file_exists($sourceDb)) {
    @copy($sourceDb, $tmpDb);
}

$dbPath = file_exists($tmpDb) ? $tmpDb : $sourceDb;

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
putenv('DB_CONNECTION=sqlite');
putenv("DB_DATABASE={$dbPath}");
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $dbPath;

putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

require __DIR__ . '/../public/index.php';
