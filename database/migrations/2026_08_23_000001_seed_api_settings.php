<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // phpcs:disable Generic.Files.LineLength
        DB::table('settings')->insertOrIgnore([
            // Zalo OA
            ['key' => 'zalo_app_id',                 'value' => '3506963003972567049',                                              'group' => 'zalo',    'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'zalo_app_secret',              'value' => 'kfWl5DU1MBNkvMP8f9Bt',                                            'group' => 'zalo',    'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'zalo_template_id_otp',         'value' => '348364',                                                          'group' => 'zalo',    'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'zalo_redirect_uri',            'value' => 'https://tinhtrongthienha.vn/zalo-oauth-callback',                 'group' => 'zalo',    'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'zalo_setup_key',               'value' => 'fe11d87c01649da3a6e6210611f89ad0',                               'group' => 'zalo',    'type' => 'text', 'created_at' => $now, 'updated_at' => $now],

            // VNPay
            ['key' => 'vnpay_tmn_code',               'value' => 'LOHIGAT1',                                                       'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vnpay_hash_secret',            'value' => 'VQRYC0V6I2KDL4VNH6RU2Z05CCJD3QFU',                             'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vnpay_url',                    'value' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',              'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vnpay_return_url',             'value' => 'http://127.0.0.1:8010/payments/vnpay/return',                    'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],

            // VNPT Pay
            ['key' => 'vnptpay_merchant_service_id',  'value' => '8202',                                                           'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vnptpay_secret_key',           'value' => '552e7a6baec3667e4ea1158448b8de53',                               'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vnptpay_base_url',             'value' => 'https://api-gw-dev.vnptmoney.com.vn/rest/payment/v1.0.6',        'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vnptpay_api_key',              'value' => 'eyJ4NXQiOiJOV1F3T1RSa01qQTVPV0ZqTm1VMk56QmxOVE0zWkRVd09EVXpZall3Wm1KbFpUTmtaREE0T0RVNFpUVXdaR0l3Tm1VeU1tWm1aVE5oWkRreU5qUTJaQSIsImtpZCI6Ik5XUXdPVFJrTWpBNU9XRmpObVUyTnpCbE5UTTNaRFV3T0RVellqWXdabUpsWlROa1pEQTRPRFU0WlRVd1pHSXdObVV5TW1abVpUTmhaRGt5TmpRMlpBX1JTMjU2IiwidHlwIjoiYXQrand0IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiIxNzE5YjM1YS1jNjQ1LTQwZmItOTJhZi00NDM5MzRmMjBkZjMiLCJhdXQiOiJBUFBMSUNBVElPTiIsImF1ZCI6Ilg5azhwMDdJdGNnckUwd0VrekNCTlBUcTMwQWEiLCJuYmYiOjE3NjAwODEyMjgsImF6cCI6Ilg5azhwMDdJdGNnckUwd0VrekNCTlBUcTMwQWEiLCJzY29wZSI6ImRlZmF1bHQiLCJpc3MiOiJodHRwczovL2xvY2FsaG9zdDo5NDQzL29hdXRoMi90b2tlbiIsImV4cCI6MjA3NTQ0MTIyOCwiaWF0IjoxNzYwMDgxMjI4LCJqdGkiOiIzMGRhMGJlYS05MWVhLTRiMzAtYWI1OC1lNmQwNDZlOTY3NzQiLCJjbGllbnRfaWQiOiJYOWs4cDA3SXRjZ3JFMHdFa3pDQk5QVHEzMEFhIn0.sTOeOZwWdI9dXgmbLK_dDLYg-qd-YAdVwHxItjns98CFwCw9VeCasmhPeO4jhJVDqk1XaSdzC1iH3Wv27OozAccrXJyGRgnbG9TGyKg9Dpum6H-I1UMJRo5BQvPjJGe_fyln-mLiHUFZ-TaZIEuPF_UBB9pAb0A3Zj9rmG8xHRHQLRHjmC5EGc5YIcMDzHmyNEUfeM9KtVJFOStCoV0-4DbZPeoYCFNpeQ8RyKZT4f7eriClRookdTcO_FgzULVLpvhD6bn0ab8IkR3Lnsi5_rBEQ31y5uGPsNWLjimGrf7iZHfp0dW2jLPrFTxcwaWkjuYvMy13GFBAktutr8oFPg', 'group' => 'payment', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],

            // CDN (read from admin .env — CDN_URL and CDN_API_KEY are defined there)
            ['key' => 'cdn_url',                      'value' => env('CDN_URL', ''),                                                'group' => 'cdn',     'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cdn_api_key',                  'value' => env('CDN_API_KEY', ''),                                           'group' => 'cdn',     'type' => 'text', 'created_at' => $now, 'updated_at' => $now],

            // Anthropic AI (read from admin .env — ANTHROPIC_* are defined there)
            ['key' => 'anthropic_api_key',            'value' => env('ANTHROPIC_API_KEY', ''),                                     'group' => 'ai',      'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'anthropic_model',              'value' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),                      'group' => 'ai',      'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
        ]);
        // phpcs:enable
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'zalo_app_id', 'zalo_app_secret', 'zalo_template_id_otp', 'zalo_redirect_uri', 'zalo_setup_key',
            'vnpay_tmn_code', 'vnpay_hash_secret', 'vnpay_url', 'vnpay_return_url',
            'vnptpay_merchant_service_id', 'vnptpay_secret_key', 'vnptpay_api_key', 'vnptpay_base_url',
            'cdn_url', 'cdn_api_key',
            'anthropic_api_key', 'anthropic_model',
        ])->delete();
    }
};
