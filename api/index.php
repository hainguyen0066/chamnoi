<?php

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

// Đảm bảo cache & session không gọi database nếu chưa sẵn sàng
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';

require __DIR__ . '/../public/index.php';
