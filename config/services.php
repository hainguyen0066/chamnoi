<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Anthropic (Claude) — tính năng AI hỗ trợ viết bài trong admin.
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
        'verbose' => env('ANTHROPIC_VERBOSE', false),
    ],

    // Cổng thanh toán VNPay (sandbox: https://sandbox.vnpayment.vn).
    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE', ''),
        'hash_secret' => env('VNPAY_HASH_SECRET', ''),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL', ''),
    ],

    // Cổng thanh toán VNPT Money (VNPT Pay) v1.0.6.
    'vnptpay' => [
        'merchant_service_id' => env('VNPTPAY_MERCHANT_SERVICE_ID', ''),
        'secret_key' => env('VNPTPAY_SECRET_KEY', ''),
        'api_key' => env('VNPTPAY_API_KEY', ''),
        'base_url' => env('VNPTPAY_BASE_URL', 'https://api-gw-dev.vnptmoney.com.vn/rest/payment/v1.0.6'),
        'timeout' => 30,
        // Local dev: sandbox VNPT dùng chứng chỉ chưa tin cậy được bởi php.ini => false.
        'verify_ssl' => env('VNPTPAY_VERIFY_SSL', true),
    ],

    // Zalo OA (ZNS) — gửi OTP xác thực số điện thoại khi đăng ký.
    // Bootstrap 1 lần: truy cập /zalo-authorize?key=<setup_key> để lấy token đầu tiên.
    'zalo_oa' => [
        'app_id' => env('ZALO_OA_APP_ID', ''),
        'app_secret' => env('ZALO_OA_APP_SECRET', ''),
        'template_id_otp' => env('ZALO_OA_TEMPLATE_ID_OTP', ''),
        'redirect_uri' => env('ZALO_OA_REDIRECT_URI', ''),
        'setup_key' => env('ZALO_OA_SETUP_KEY', ''),
        'debug' => env('ZALO_OA_DEBUG', false),
        'timeout' => 30,
    ],

    // CDN Image Server (cdnimg.tinhtrongthienha.vn)
    'cdn' => [
        'url'     => env('CDN_URL', 'https://cdnimg.tinhtrongthienha.vn'),
        'api_key' => env('CDN_API_KEY', ''),
    ],

    // API tài khoản game (Tình Trong Thiên Hạ v2) — đổi mật khẩu cho user từ admin.
    // Giá trị thật lấy từ bảng settings (tab Game API), env chỉ là fallback.
    'game_api' => [
        'base_url' => env('GAME_API_BASE_URL', 'http://api.tinhtrongthienha.vn/v2'),
        'key' => env('GAME_API_KEY', ''),
        'timeout' => 10,
    ],

];
