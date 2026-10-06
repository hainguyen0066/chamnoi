<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $val) {
            $setting = SiteSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $val]);
            }
        }

        return back()->with('success', 'Đã lưu cấu hình hệ thống thành công!');
    }
}
