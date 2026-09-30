<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CdnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Cấu hình website: form key-value theo nhóm + nút bật/tắt chế độ bảo trì.
 * Bảo trì dùng setting DB (maintenance_mode=1/0) — chỉ ảnh hưởng frontend,
 * không khoá admin panel.
 */
class SettingController extends Controller
{
    public function edit(): View
    {
        $s           = Setting::query()->pluck('value', 'key');
        $isDown      = ($s->get('maintenance_mode') ?? '0') === '1';
        $frontendUrl = env('FRONTEND_URL', 'http://127.0.0.1:8010');

        return view('admin.settings.edit', [
            's'                 => $s,
            'isDown'            => $isDown,
            'maintenanceSecret' => $s->get('maintenance_secret', ''),
            'frontendUrl'       => $frontendUrl,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings'               => ['nullable', 'array'],
            'settings.*'             => ['nullable', 'string', 'max:5000'],
            'settings.vnpay_url'         => ['nullable', 'string', $this->urlRule('Payment URL')],
            'settings.vnpay_return_url'  => ['nullable', 'string', $this->urlRule('Return URL')],
            'settings.download_appstore' => ['nullable', 'string', $this->urlRule('Link App Store', false)],
            'settings.download_ggplay'   => ['nullable', 'string', $this->urlRule('Link Google Play')],
            'settings.download_apk'      => ['nullable', 'string', $this->urlRule('Link tải APK')],
            'settings.download_emulator' => ['nullable', 'string', $this->urlRule('Link giả lập')],
            'settings.appstore_redirect_store_link' => ['nullable', 'string', 'max:2000'],
            'settings.appstore_redirect_web_link'   => ['nullable', 'string', $this->urlRule('Link Web Store dự phòng')],
            'settings.community_links'   => ['nullable', 'string', 'max:5000', $this->communityLinksRule()],
            'maintenance_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
        ]);

        $knownKeys = Setting::query()->pluck('key')->all();

        // Ảnh bảo trì → upload lên CDN
        if ($request->hasFile('maintenance_image_file')) {
            $file = $request->file('maintenance_image_file');
            $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            try {
                $cdn = app(CdnService::class);
                $url = $cdn->upload($file, 'maintenance.' . $ext);
                if (in_array('maintenance_image', $knownKeys, true)) {
                    Setting::set('maintenance_image', $url);
                }
            } catch (\RuntimeException $e) {
                Log::warning('SettingController: CDN upload failed', ['error' => $e->getMessage()]);
                return redirect()->route('admin.settings.edit')
                    ->with('error', 'Upload ảnh lên CDN thất bại: ' . $e->getMessage());
            }
        }

        // Nút nạp thẻ KHÔNG lưu ở đây: công tắc nằm trong form riêng (toggleDeposit, route admin.settings.deposit).
        // Các form "Lưu cấu hình" không có checkbox này, nên đọc $request->boolean('deposit_enabled') ở đây
        // luôn ra false và tắt nạp thẻ mỗi lần lưu cấu hình khác. deposit_enabled cũng nằm trong $skipKeys bên dưới.

        // Các setting text thông thường
        $skipKeys = ['deposit_enabled', 'maintenance_mode', 'maintenance_image', 'maintenance_secret'];
        foreach ($request->input('settings', []) as $key => $value) {
            if (in_array($key, $knownKeys, true) && !in_array($key, $skipKeys, true)) {
                Setting::set($key, $value ?? '');
            }
        }

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Đã lưu cấu hình.')
            ->with('active_tab', $request->input('_tab', 'chung'));
    }

    /**
     * Luật kiểm tra URL cấu hình cổng thanh toán.
     *
     * `filter_var(..., FILTER_VALIDATE_URL)` chấp nhận cả `https://ti/abc` —
     * host không có dấu chấm vẫn hợp lệ về mặt cú pháp. Đúng là trường hợp đã
     * xảy ra thật: Return URL bị lưu cụt thành `https://ti/payments/vnpay/return`,
     * VNPay không dùng được nên đẩy người dùng về trang mặc định của họ và
     * giao dịch không được cộng xu.
     *
     * Nên ngoài cú pháp còn bắt buộc host phải là tên miền có dấu chấm, một
     * địa chỉ IP, hoặc localhost. Không dùng `active_url` vì nó tra DNS thật,
     * sẽ chặn luôn các domain chỉ khai trong file hosts khi dev.
     */
    /**
     * Menu "Cộng đồng": mỗi dòng không trống phải là "Tên | http(s)://URL", tên ≤ 60 ký tự, tối đa 20 dòng.
     * Frontend đọc cùng định dạng (frontend/partials/_header.blade.php).
     */
    private function communityLinksRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $value)), fn ($l) => $l !== ''));

            if (count($lines) > 20) {
                $fail('Menu Cộng đồng tối đa 20 dòng.');

                return;
            }

            foreach ($lines as $i => $line) {
                $parts = array_map('trim', explode('|', $line, 2));
                $n = $i + 1;

                if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                    $fail("Menu Cộng đồng, dòng {$n}: cần dạng \"Tên | https://link\".");

                    return;
                }
                if (mb_strlen($parts[0]) > 60) {
                    $fail("Menu Cộng đồng, dòng {$n}: tên dài quá 60 ký tự.");

                    return;
                }
                $scheme = strtolower((string) parse_url($parts[1], PHP_URL_SCHEME));
                if (filter_var($parts[1], FILTER_VALIDATE_URL) === false || ! in_array($scheme, ['http', 'https'], true)) {
                    $fail("Menu Cộng đồng, dòng {$n}: link phải là URL bắt đầu bằng http:// hoặc https://.");

                    return;
                }
            }
        };
    }

    private function urlRule(string $label, bool $requireHttp = true): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($label, $requireHttp): void {
            $value = trim((string) $value);

            if ($value === '') {
                return;
            }

            if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                $fail("{$label} không phải là một URL hợp lệ.");

                return;
            }

            $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

            if ($requireHttp && ! in_array($scheme, ['http', 'https'], true)) {
                $fail("{$label} phải bắt đầu bằng http:// hoặc https://.");

                return;
            }

            // Link kiểu app scheme (itms-apps://, market://...) không có host dạng tên miền
            // nên bỏ qua kiểm tra host bên dưới với các scheme khác http/https.
            if (! in_array($scheme, ['http', 'https'], true)) {
                return;
            }

            $host = (string) parse_url($value, PHP_URL_HOST);
            $isIp = filter_var($host, FILTER_VALIDATE_IP) !== false;

            if (! $isIp && $host !== 'localhost' && ! str_contains($host, '.')) {
                $fail("{$label}: tên miền \"{$host}\" trông như bị nhập thiếu. Hãy nhập đầy đủ, ví dụ https://tinhtrongthienha.vn/payments/vnpay/return");
            }
        };
    }

    public function toggleMaintenance(): RedirectResponse
    {
        $isDown = Setting::get('maintenance_mode') === '1';

        if ($isDown) {
            Setting::set('maintenance_mode', '0');
            Setting::set('maintenance_secret', '');

            return redirect()->route('admin.settings.edit')
                ->with('status', 'Website frontend đã mở hoạt động trở lại.')
                ->with('active_tab', 'maintenance');
        }

        $secret = Str::random(32);
        Setting::set('maintenance_mode', '1');
        Setting::set('maintenance_secret', $secret);

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Đã bật chế độ bảo trì. Dùng link bypass để vào frontend khi cần.')
            ->with('active_tab', 'maintenance');
    }

    public function saveWhitelist(Request $request): RedirectResponse
    {
        $request->validate([
            'maintenance_whitelist' => ['nullable', 'string', 'max:5000'],
        ]);

        // Chuẩn hoá: trim từng dòng, bỏ dòng rỗng, lưu lại
        $raw   = (string) $request->input('maintenance_whitelist', '');
        $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $raw))));
        $value = implode("\n", $lines);

        // Dùng updateOrCreate trực tiếp để đảm bảo group đúng khi tạo mới lần đầu
        Setting::query()->updateOrCreate(
            ['key' => 'maintenance_whitelist'],
            ['value' => $value, 'group' => 'maintenance']
        );
        \Illuminate\Support\Facades\Cache::forget(Setting::CACHE_KEY);

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Đã lưu danh sách IP whitelist.')
            ->with('active_tab', 'maintenance');
    }

    public function toggleDeposit(Request $request): RedirectResponse
    {
        $enabled = $request->boolean('deposit_enabled') ? '1' : '0';
        Setting::set('deposit_enabled', $enabled);
        $msg = $enabled === '1' ? 'Đã bật hiển thị nút Nạp Thẻ.' : 'Đã ẩn nút Nạp Thẻ.';
        return redirect()->route('admin.settings.edit')
            ->with('status', $msg)
            ->with('active_tab', 'chung');
    }

    public function clearCache(Request $request): RedirectResponse
    {
        $types = $request->input('types', []);

        $ran = [];

        if (empty($types) || in_array('cache', $types, true)) {
            Artisan::call('cache:clear');
            $ran[] = 'Application cache';
        }

        if (empty($types) || in_array('view', $types, true)) {
            Artisan::call('view:clear');
            $ran[] = 'View cache';
        }

        if (empty($types) || in_array('config', $types, true)) {
            Artisan::call('config:clear');
            $ran[] = 'Config cache';
        }

        if (empty($types) || in_array('route', $types, true)) {
            Artisan::call('route:clear');
            $ran[] = 'Route cache';
        }

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Đã xóa: ' . implode(', ', $ran) . '.')
            ->with('active_tab', 'system');
    }

    /**
     * Đổi mật khẩu đăng nhập của chính admin đang đăng nhập (guard "admin").
     * Không liên quan tới mật khẩu tài khoản game của user — đó là UserController::changePassword.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'password.min'              => 'Mật khẩu mới tối thiểu 8 ký tự.',
            'password.confirmed'        => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (! Hash::check((string) $request->input('current_password'), $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])
                ->with('active_tab', 'system');
        }

        $admin->forceFill(['password' => $request->input('password')])->save();

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Đã đổi mật khẩu đăng nhập admin.')
            ->with('active_tab', 'system');
    }
}
