<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Khởi tạo các dòng cấu hình mặc định cho website.
 * Chạy lại an toàn (updateOrCreate) — không tạo trùng.
 */
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // group general
            ['key' => 'site_name',        'value' => 'Võ Lâm',                'type' => 'text',   'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Game Võ Lâm Truyền Kỳ', 'type' => 'text',   'group' => 'general'],
            ['key' => 'logo',             'value' => '',                       'type' => 'image',  'group' => 'general'],
            ['key' => 'deposit_enabled',  'value' => '1',                      'type' => 'toggle', 'group' => 'general'],

            // group contact (kept in DB for backward compat, hidden in UI)
            ['key' => 'contact_phone',   'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_email',   'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => '', 'type' => 'text', 'group' => 'contact'],

            // group social
            ['key' => 'social_fanpage', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social_zalo',    'value' => '', 'type' => 'text', 'group' => 'social'],

            // group maintenance
            ['key' => 'maintenance_mode',  'value' => '0',                                          'type' => 'toggle', 'group' => 'maintenance'],
            ['key' => 'maintenance_image', 'value' => 'frontend/assets/images/baotri.jpg',          'type' => 'image',  'group' => 'maintenance'],
            ['key' => 'maintenance_secret','value' => '',                                            'type' => 'text',   'group' => 'maintenance'],
        ];

        foreach ($defaults as $row) {
            Setting::query()->updateOrCreate(
                ['key' => $row['key']],
                ['value' => $row['value'], 'type' => $row['type'], 'group' => $row['group']]
            );
        }
    }
}
