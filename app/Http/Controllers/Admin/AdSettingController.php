<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::whereIn('group', ['ads', 'seo', 'analytics'])->get()->keyBy('key');

        return view('admin.ads.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'adsense_client_id' => 'ads',
            'adsense_auto_ads_enabled' => 'ads',
            'ad_header_banner_code' => 'ads',
            'ad_header_banner_enabled' => 'ads',
            'ad_in_article_code' => 'ads',
            'ad_in_article_enabled' => 'ads',
            'ad_sidebar_affiliate_code' => 'ads',
            'ad_sidebar_affiliate_enabled' => 'ads',
            'google_analytics_id' => 'analytics',
        ];

        foreach ($fields as $key => $group) {
            $value = $request->input($key);
            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'label' => ucwords(str_replace('_', ' ', $key)),
                ]
            );
        }

        return redirect()->route('admin.ads.index')->with('success', 'Đã lưu cấu hình Quảng cáo & Tiếp thị liên kết thành công!');
    }
}
