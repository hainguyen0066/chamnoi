<x-admin-layout>
    <x-slot name="header">Cấu hình hệ thống</x-slot>

    {{-- Flash --}}
    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
            <p class="font-medium mb-1">Chưa lưu được, vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tab navigation --}}
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
        @foreach ([
            'chung'       => 'Chung',
            'zalo'        => 'Zalo OA',
            'payment'     => 'Thanh toán',
            'cdn'         => 'CDN',
            'ai'          => 'AI Claude',
            'game'        => 'Game API',
            'maintenance' => 'Bảo trì',
            'system'      => 'Hệ thống',
        ] as $tabId => $tabLabel)
            <button type="button" data-tab="{{ $tabId }}"
                    class="tab-btn whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 -mb-px transition-colors">
                {{ $tabLabel }}
            </button>
        @endforeach
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: CHUNG                                                       --}}
    {{-- =============================================================== --}}
    <div id="tab-chung" class="tab-panel">

        {{-- Thông tin chung + mạng xã hội --}}
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tab" value="chung">

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">Thông tin website</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach (['site_name' => 'Tên website', 'site_description' => 'Mô tả website', 'logo' => 'Logo (đường dẫn ảnh)'] as $key => $label)
                        <div>
                            <label for="f-{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <input type="text" id="f-{{ $key }}" name="settings[{{ $key }}]"
                                   value="{{ old('settings.'.$key, $s->get($key, '')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">Mạng xã hội</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach (['social_fanpage' => 'Fanpage Facebook', 'social_youtube' => 'Kênh YouTube', 'social_zalo' => 'Zalo OA'] as $key => $label)
                        <div>
                            <label for="f-{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <input type="text" id="f-{{ $key }}" name="settings[{{ $key }}]"
                                   value="{{ old('settings.'.$key, $s->get($key, '')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    @endforeach
                </div>

                {{-- Menu "Cộng đồng" (dropdown ở menu top frontend): mỗi dòng "Tên | URL", theo đúng thứ tự hiển thị. --}}
                <div class="mt-5">
                    <label for="f-community_links" class="block text-sm font-medium text-gray-700 mb-1">Menu Cộng đồng (dropdown)</label>
                    <textarea id="f-community_links" name="settings[community_links]" rows="8"
                              placeholder="Fanpage | https://facebook.com/...&#10;Nhóm Facebook | https://facebook.com/groups/...&#10;Nhóm Zalo 1 | https://zalo.me/g/..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono">{{ old('settings.community_links', $s->get('community_links', '')) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Mỗi dòng một link theo dạng <strong>Tên hiển thị | https://link</strong>, tối đa 20 dòng. Thứ tự dòng là thứ tự trên menu. Để trống thì menu dùng Fanpage và Zalo OA ở trên.</p>
                    @error('settings.community_links')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Link tải game: 4 icon menu top trang chủ. Trống => frontend hiện popup "Đang cập nhật" --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-1">Link tải game (icon trang chủ)</h2>
                <p class="text-xs text-gray-400 mb-4">Để trống ô nào thì khi click icon tương ứng trên trang chủ sẽ hiện popup <strong>"Đang cập nhật"</strong> thay vì chuyển hướng.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'download_appstore' => ['App Store (iOS)',        'https://apps.apple.com/...'],
                        'download_ggplay'   => ['Google Play (Android)',  'https://play.google.com/store/apps/details?id=...'],
                        'download_apk'      => ['Tải APK (link redirect)', 'https://.../game.apk'],
                        'download_emulator' => ['Giả lập PC (link redirect)', 'https://.../gia-lap'],
                    ] as $key => [$label, $placeholder])
                        <div>
                            <label for="f-{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <input type="url" id="f-{{ $key }}" name="settings[{{ $key }}]"
                                   value="{{ old('settings.'.$key, $s->get($key, '')) }}"
                                   placeholder="{{ $placeholder }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('settings.'.$key) border-red-400 @enderror">
                            @if (trim((string) $s->get($key, '')) === '')
                                <p class="text-xs text-amber-600 mt-1">Chưa cấu hình — icon này đang hiện popup "Đang cập nhật".</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Trang trung gian /app-store-download: App Store (iOS) ở trên trỏ vào đây, --}}
            {{-- 2 link bên dưới quyết định hành vi của trang đó. --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-1">Trang chuyển hướng App Store</h2>
                <p class="text-xs text-gray-400 mb-4">
                    "App Store (iOS)" ở trên trỏ tới <code class="bg-gray-100 px-1 rounded">{{ url('/app-store-download') }}</code> —
                    trang này sẽ tự redirect người dùng theo 2 link cấu hình dưới đây.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'appstore_redirect_store_link' => ['Link mở app', 'itms-beta://testflight.apple.com/join/... hoặc https://apps.apple.com/...'],
                        'appstore_redirect_web_link'   => ['Link Web Store dự phòng', 'https://testflight.apple.com/join/... hoặc https://apps.apple.com/...'],
                    ] as $key => [$label, $placeholder])
                        <div>
                            <label for="f-{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <input type="text" id="f-{{ $key }}" name="settings[{{ $key }}]"
                                   value="{{ old('settings.'.$key, $s->get($key, '')) }}"
                                   placeholder="{{ $placeholder }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('settings.'.$key) border-red-400 @enderror">
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-3">
                    "Link mở app" có thể là scheme tuỳ ý (<code>itms-beta://</code>...) nên không bắt buộc đúng định dạng URL http(s).
                    "Link Web Store dự phòng" phải là link http(s) hợp lệ.
                </p>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">Giới hạn đăng ký</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="f-max_accounts_per_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Số tài khoản tối đa mỗi số điện thoại
                        </label>
                        <input type="number" id="f-max_accounts_per_phone" name="settings[max_accounts_per_phone]"
                               value="{{ old('settings.max_accounts_per_phone', $s->get('max_accounts_per_phone', '10')) }}"
                               min="1" max="1000"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Số lượng tài khoản có thể đăng ký bằng cùng một số điện thoại (mặc định: 10).</p>
                    </div>
                    <div>
                        <label for="f-max_accounts_per_email" class="block text-sm font-medium text-gray-700 mb-1">
                            Số tài khoản tối đa mỗi email
                        </label>
                        <input type="number" id="f-max_accounts_per_email" name="settings[max_accounts_per_email]"
                               value="{{ old('settings.max_accounts_per_email', $s->get('max_accounts_per_email', '10')) }}"
                               min="1" max="1000"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Số lượng tài khoản có thể đăng ký bằng cùng một email (mặc định: 10).</p>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-primary mb-6">
                Lưu cấu hình
            </button>
        </form>

        {{-- Nút nạp thẻ (checkbox auto-submit) --}}
        @php $depositEnabled = ($s->get('deposit_enabled', '1')) === '1'; @endphp
        <form method="POST" action="{{ route('admin.settings.deposit') }}" id="deposit-form">
            @csrf
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">Nút nạp thẻ</h2>
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" name="deposit_enabled" value="1"
                               class="sr-only peer" {{ $depositEnabled ? 'checked' : '' }}
                               onchange="document.getElementById('deposit-form').submit()">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-0.5 after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Hiển thị nút Nạp Thẻ trên frontend</span>
                        <p class="text-xs text-gray-400 mt-0.5">Tắt để ẩn nút Nạp Thẻ ở tất cả vị trí trên trang chủ</p>
                    </div>
                </label>
            </div>
        </form>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: ZALO OA                                                     --}}
    {{-- =============================================================== --}}
    <div id="tab-zalo" class="tab-panel hidden">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tab" value="zalo">

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-1">Zalo OA</h2>
                <p class="text-xs text-gray-400 mb-4">
                    Sau khi thay đổi App ID hoặc App Secret, cần ủy quyền lại tại
                    <a href="{{ url('/zalo-authorize') }}?key={{ $s->get('zalo_setup_key', '') }}"
                       target="_blank" class="text-indigo-600 hover:underline">/zalo-authorize</a>.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App ID</label>
                        <input type="text" name="settings[zalo_app_id]"
                               value="{{ old('settings.zalo_app_id', $s->get('zalo_app_id', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App Secret</label>
                        @include('admin.settings._password_field', ['id' => 'f-zalo-secret', 'name' => 'settings[zalo_app_secret]', 'value' => old('settings.zalo_app_secret', $s->get('zalo_app_secret', ''))])
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Template ID OTP</label>
                        <input type="text" name="settings[zalo_template_id_otp]"
                               value="{{ old('settings.zalo_template_id_otp', $s->get('zalo_template_id_otp', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Setup Key</label>
                        @include('admin.settings._password_field', ['id' => 'f-zalo-setup', 'name' => 'settings[zalo_setup_key]', 'value' => old('settings.zalo_setup_key', $s->get('zalo_setup_key', ''))])
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Redirect URI</label>
                        <input type="text" name="settings[zalo_redirect_uri]"
                               value="{{ old('settings.zalo_redirect_uri', $s->get('zalo_redirect_uri', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                </div>
            </div>

            {{-- Bật/tắt xác thực OTP khi đăng ký --}}
            @php $otpRequired = ($s->get('otp_required', '1')) !== '0'; @endphp
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">Xác thực OTP khi đăng ký</h2>
                {{-- Hidden "0" đi trước để checkbox bỏ tick vẫn gửi được giá trị tắt. --}}
                <input type="hidden" name="settings[otp_required]" value="0">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" name="settings[otp_required]" value="1"
                               class="sr-only peer" {{ $otpRequired ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-0.5 after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Bắt buộc xác thực OTP khi đăng ký</span>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Tắt để bỏ qua bước gửi/nhập OTP — chỉ dùng khi test local, luôn bật lại trên production.
                        </p>
                    </div>
                </label>
            </div>

            <button type="submit"
                    class="btn btn-primary">
                Lưu cấu hình
            </button>
        </form>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: THANH TOÁN                                                  --}}
    {{-- =============================================================== --}}
    <div id="tab-payment" class="tab-panel hidden">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tab" value="payment">

            {{-- VNPay --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">VNPay</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TMN Code</label>
                        <input type="text" name="settings[vnpay_tmn_code]"
                               value="{{ old('settings.vnpay_tmn_code', $s->get('vnpay_tmn_code', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hash Secret</label>
                        @include('admin.settings._password_field', ['id' => 'f-vnpay-hash', 'name' => 'settings[vnpay_hash_secret]', 'value' => old('settings.vnpay_hash_secret', $s->get('vnpay_hash_secret', ''))])
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment URL</label>
                        <input type="text" name="settings[vnpay_url]"
                               value="{{ old('settings.vnpay_url', $s->get('vnpay_url', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Return URL</label>
                        <input type="text" name="settings[vnpay_return_url]"
                               value="{{ old('settings.vnpay_return_url', $s->get('vnpay_return_url', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @php
                            // Cảnh báo ngay nếu giá trị đang lưu có host không hợp lệ
                            // (đã từng bị lưu cụt thành https://ti/... khiến VNPay
                            // đẩy người dùng về trang mặc định của họ).
                            $rtHost = parse_url((string) $s->get('vnpay_return_url', ''), PHP_URL_HOST);
                            $rtBroken = $s->get('vnpay_return_url')
                                && $rtHost
                                && $rtHost !== 'localhost'
                                && ! str_contains($rtHost, '.')
                                && filter_var($rtHost, FILTER_VALIDATE_IP) === false;
                        @endphp
                        @if ($rtBroken)
                            <p class="text-xs text-red-600 mt-1 font-medium">
                                Tên miền "{{ $rtHost }}" trông như bị nhập thiếu. VNPay sẽ bỏ qua và đẩy người
                                dùng về trang mặc định của họ — giao dịch không được cộng KPoint.
                            </p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            Trang người dùng quay về sau khi thanh toán. <strong>Khác với IPN URL</strong> —
                            IPN đăng ký trực tiếp với VNPay, không cấu hình ở đây.
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        @php
                            // FRONTEND_URL có thể được nhập kèm hoặc không kèm scheme.
                            $ipnBase = rtrim($frontendUrl, '/');
                            $ipnBase = \Illuminate\Support\Str::startsWith($ipnBase, ['http://', 'https://']) ? $ipnBase : 'https://' . $ipnBase;
                        @endphp
                        <label class="block text-sm font-medium text-gray-700 mb-1">IPN URL (đăng ký với VNPay)</label>
                        <input type="text" readonly value="{{ $ipnBase }}/payments/vnpay/ipn"
                               class="w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 shadow-sm text-sm font-mono">
                        <p class="text-xs text-gray-400 mt-1">
                            Chỉ để tham khảo — gửi đường dẫn này cho VNPay để họ cấu hình phía cổng.
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Whitelist IP cho IPN</label>
                        <textarea name="settings[vnpay_ipn_whitelist]" rows="4"
                                  placeholder="113.160.92.202&#10;113.160.92.0/24&#10;113.52.14.*"
                                  class="w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">{{ old('settings.vnpay_ipn_whitelist', $s->get('vnpay_ipn_whitelist', '')) }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">
                            Mỗi dòng một IP, dải CIDR (<code>113.160.92.0/24</code>) hoặc wildcard
                            (<code>113.160.92.*</code>). Dòng bắt đầu bằng <code>#</code> là ghi chú.
                            <strong>Để trống = không giới hạn.</strong>
                            IP ngoài danh sách sẽ bị từ chối với <code>RspCode=97</code> và vẫn được ghi vào
                            <a href="{{ route('admin.webhook-logs.index') }}" class="text-indigo-600 hover:underline">Log Webhook</a>.
                        </p>
                        <p class="text-xs text-amber-600 mt-1">
                            Lưu ý: đây là chặn ở tầng ứng dụng. Nếu server có firewall (BaoTa/CSF) thì phải
                            whitelist dải IP VNPay ở đó nữa, nếu không request bị chặn trước khi tới Laravel
                            và sẽ không có log.
                        </p>
                    </div>
                </div>
            </div>

            {{-- VNPT Pay --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">VNPT Pay</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Merchant Service ID</label>
                        <input type="text" name="settings[vnptpay_merchant_service_id]"
                               value="{{ old('settings.vnptpay_merchant_service_id', $s->get('vnptpay_merchant_service_id', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                        @include('admin.settings._password_field', ['id' => 'f-vnpt-secret', 'name' => 'settings[vnptpay_secret_key]', 'value' => old('settings.vnptpay_secret_key', $s->get('vnptpay_secret_key', ''))])
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                        <input type="text" name="settings[vnptpay_base_url]"
                               value="{{ old('settings.vnptpay_base_url', $s->get('vnptpay_base_url', '')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            API Key (JWT)
                            <span class="font-normal text-gray-400 ml-1">— token dài, dán toàn bộ vào đây</span>
                        </label>
                        <div class="relative">
                            <textarea id="f-vnpt-api" name="settings[vnptpay_api_key]" rows="4"
                                      class="w-full rounded-md border-gray-300 shadow-sm text-sm font-mono text-xs pr-10"
                                      >{{ old('settings.vnptpay_api_key', $s->get('vnptpay_api_key', '')) }}</textarea>
                            <button type="button" onclick="toggleTextarea('f-vnpt-api')"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
                                    title="Ẩn/hiện">
                                @include('admin.settings._eye_icon')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-primary">
                Lưu cấu hình
            </button>
        </form>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: CDN                                                         --}}
    {{-- =============================================================== --}}
    <div id="tab-cdn" class="tab-panel hidden">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tab" value="cdn">

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">CDN Server</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">CDN URL</label>
                        <input type="text" name="settings[cdn_url]"
                               value="{{ old('settings.cdn_url', $s->get('cdn_url', '')) }}"
                               placeholder="https://cdnimg.tinhtrongthienha.vn"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        @include('admin.settings._password_field', ['id' => 'f-cdn-key', 'name' => 'settings[cdn_api_key]', 'value' => old('settings.cdn_api_key', $s->get('cdn_api_key', ''))])
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-primary">
                Lưu cấu hình
            </button>
        </form>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: AI CLAUDE                                                   --}}
    {{-- =============================================================== --}}
    <div id="tab-ai" class="tab-panel hidden">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tab" value="ai">

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-4">Anthropic Claude</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        @include('admin.settings._password_field', ['id' => 'f-ai-key', 'name' => 'settings[anthropic_api_key]', 'value' => old('settings.anthropic_api_key', $s->get('anthropic_api_key', ''))])
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <input type="text" name="settings[anthropic_model]"
                               value="{{ old('settings.anthropic_model', $s->get('anthropic_model', 'claude-sonnet-4-5')) }}"
                               placeholder="claude-sonnet-4-5"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <p class="text-xs text-gray-400 mt-1">Ví dụ: claude-sonnet-4-5, claude-haiku-4-5-20251001</p>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-primary">
                Lưu cấu hình
            </button>
        </form>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: BẢO TRÌ                                                    --}}
    {{-- =============================================================== --}}
    <div id="tab-maintenance" class="tab-panel hidden">

        {{-- Toggle --}}
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h2 class="font-semibold text-gray-800 text-base">Chế độ bảo trì</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Trạng thái:
                        @if ($isDown)
                            <span class="font-semibold text-red-600">ĐANG BẢO TRÌ — khách thấy trang bảo trì</span>
                        @else
                            <span class="font-semibold text-green-600">Đang hoạt động bình thường</span>
                        @endif
                    </p>
                    @if ($isDown && $maintenanceSecret)
                        <p class="text-xs text-gray-500 mt-2">
                            Link vượt bảo trì (frontend):
                            <a href="{{ $frontendUrl }}/{{ $maintenanceSecret }}" target="_blank"
                               class="text-indigo-600 hover:underline break-all">{{ $frontendUrl }}/{{ $maintenanceSecret }}</a>
                        </p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.maintenance') }}"
                      onsubmit="return confirm('Bạn chắc chắn muốn {{ $isDown ? 'MỞ website hoạt động lại' : 'BẬT chế độ bảo trì' }}?');">
                    @csrf
                    <button type="submit"
                            class="btn whitespace-nowrap {{ $isDown ? 'btn-success' : 'btn-danger' }}">
                        {{ $isDown ? 'Mở website' : 'Bật bảo trì' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Ảnh bảo trì --}}
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Ảnh hiển thị khi bảo trì</h3>
            <div class="flex items-start gap-5 flex-wrap">
                <div class="shrink-0">
                    @php $mainImg = $s->get('maintenance_image', ''); @endphp
                    @if ($mainImg)
                        <img src="{{ asset($mainImg) }}" alt="Ảnh bảo trì"
                             class="h-28 w-auto rounded border border-gray-200 object-contain bg-gray-50">
                    @else
                        <div class="h-28 w-40 rounded border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
                            Chưa có ảnh
                        </div>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.update') }}"
                      enctype="multipart/form-data" class="flex-1 min-w-[220px]">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_tab" value="maintenance">
                    <label class="block text-xs text-gray-500 mb-1">Tải ảnh mới (JPG, PNG, GIF — tối đa 4 MB)</label>
                    <input type="file" name="maintenance_image_file" accept="image/*"
                           class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('maintenance_image_file')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-2 btn-xs btn-xs-primary">
                        Lưu ảnh bảo trì
                    </button>
                </form>
            </div>
        </div>

        {{-- IP Whitelist --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">IP Whitelist (bỏ qua bảo trì)</h3>
            <p class="text-xs text-gray-400 mb-3">Mỗi dòng một IP. Hỗ trợ wildcard: <code class="bg-gray-100 px-1 rounded">192.168.1.*</code></p>
            <form method="POST" action="{{ route('admin.settings.whitelist') }}">
                @csrf
                <textarea name="maintenance_whitelist" rows="5"
                          placeholder="Ví dụ:&#10;123.456.789.0&#10;192.168.1.*"
                          class="w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">{{ old('maintenance_whitelist', $s->get('maintenance_whitelist', '')) }}</textarea>
                <button type="submit" class="mt-2 btn-xs btn-xs-primary">
                    Lưu whitelist
                </button>
            </form>
        </div>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: GAME API                                                    --}}
    {{-- =============================================================== --}}
    <div id="tab-game" class="tab-panel hidden">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="_tab" value="game">

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-800 text-base mb-1">Game API</h2>
                <p class="text-xs text-gray-400 mb-4">
                    Kết nối tới máy chủ game để tạo tài khoản, nạp Kim Nguyên Bảo, đổi mật khẩu.
                    Chữ ký được tính phía máy chủ — <strong>không bao giờ để lộ Key ra trình duyệt</strong>.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                        <input type="text" name="settings[game_api_base_url]"
                               value="{{ old('settings.game_api_base_url', $s->get('game_api_base_url', 'http://api.tinhtrongthienha.vn/v2')) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                               placeholder="http://api.tinhtrongthienha.vn/v2">
                        <p class="text-xs text-gray-400 mt-1">Không có dấu / ở cuối.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                        @include('admin.settings._password_field', [
                            'id'    => 'f-game-key',
                            'name'  => 'settings[game_api_key]',
                            'value' => old('settings.game_api_key', $s->get('game_api_key', '')),
                        ])
                        <p class="text-xs text-gray-400 mt-1">Dùng để tính chữ ký md5 cho mọi lệnh gọi API.</p>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-primary">
                Lưu cấu hình
            </button>
        </form>
    </div>

    {{-- =============================================================== --}}
    {{-- TAB: HỆ THỐNG                                                  --}}
    {{-- =============================================================== --}}
    <div id="tab-system" class="tab-panel hidden">
        {{-- Đổi mật khẩu đăng nhập admin (guard "admin") --}}
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-gray-800 text-base mb-1">Đổi mật khẩu đăng nhập</h2>
            <p class="text-sm text-gray-500 mb-5">
                Đổi mật khẩu tài khoản admin đang đăng nhập ({{ auth('admin')->user()->email }}).
                Không ảnh hưởng tới mật khẩu tài khoản game của người chơi.
            </p>
            <form method="POST" action="{{ route('admin.settings.change-password') }}" class="max-w-md space-y-4">
                @csrf
                <div>
                    <label for="f-current-password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                    @include('admin.settings._password_field', ['id' => 'f-current-password', 'name' => 'current_password', 'value' => ''])
                    @error('current_password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="f-new-password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                    @include('admin.settings._password_field', ['id' => 'f-new-password', 'name' => 'password', 'value' => ''])
                    <p class="text-xs text-gray-400 mt-1">Tối thiểu 8 ký tự.</p>
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="f-new-password-confirm" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                    @include('admin.settings._password_field', ['id' => 'f-new-password-confirm', 'name' => 'password_confirmation', 'value' => ''])
                </div>
                <button type="submit" class="btn btn-primary">
                    Đổi mật khẩu
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-gray-800 text-base mb-1">Xóa cache</h2>
            <p class="text-sm text-gray-500 mb-5">
                Chọn loại cache muốn xóa rồi nhấn nút. Sau khi xóa, lần request đầu tiên sẽ chậm hơn một chút do phải tái tạo cache.
            </p>

            <form method="POST" action="{{ route('admin.settings.cache.clear') }}"
                  onsubmit="return confirm('Xóa cache đã chọn?');">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                    @foreach ([
                        'cache'  => ['Xóa Application Cache', 'Cache::flush() — xóa toàn bộ dữ liệu cache (blocks, settings, …)'],
                        'view'   => ['Xóa View Cache', 'Xóa các file Blade đã compile — Laravel tự compile lại khi cần'],
                        'config' => ['Xóa Config Cache', 'Xóa config.php đã cache — áp dụng thay đổi .env ngay lập tức'],
                        'route'  => ['Xóa Route Cache', 'Xóa route cache — cần thiết sau khi thêm/xóa route'],
                    ] as $type => [$title, $desc])
                        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="types[]" value="{{ $type }}"
                                   class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                            <div>
                                <span class="block text-sm font-medium text-gray-700">{{ $title }}</span>
                                <span class="block text-xs text-gray-400 mt-0.5">{{ $desc }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-danger">
                    Xóa cache đã chọn
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-semibold text-gray-800 text-base mb-1">Thông tin PHP / Laravel</h2>
            <table class="text-sm text-gray-600 w-full">
                <tbody class="divide-y divide-gray-100">
                    <tr class="py-2"><td class="py-2 pr-4 text-gray-400 w-40">Laravel</td><td class="py-2 font-mono">{{ app()->version() }}</td></tr>
                    <tr class="py-2"><td class="py-2 pr-4 text-gray-400">PHP</td><td class="py-2 font-mono">{{ PHP_VERSION }}</td></tr>
                    <tr class="py-2"><td class="py-2 pr-4 text-gray-400">Môi trường</td><td class="py-2 font-mono">{{ app()->environment() }}</td></tr>
                    <tr class="py-2"><td class="py-2 pr-4 text-gray-400">Cache driver</td><td class="py-2 font-mono">{{ config('cache.default') }}</td></tr>
                    <tr class="py-2"><td class="py-2 pr-4 text-gray-400">Queue driver</td><td class="py-2 font-mono">{{ config('queue.default') }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- =============================================================== --}}
    {{-- Tab switching JS                                                 --}}
    {{-- =============================================================== --}}
    <script>
    (function () {
        const TAB_ACTIVE   = ['border-indigo-600', 'text-indigo-600'];
        const TAB_INACTIVE = ['border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300'];

        const btns   = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.tab-panel');

        function activate(name) {
            btns.forEach(b => {
                const active = b.dataset.tab === name;
                b.classList.toggle('border-b-2', true);
                TAB_ACTIVE.forEach(c => b.classList.toggle(c, active));
                TAB_INACTIVE.forEach(c => b.classList.toggle(c, !active));
            });
            panels.forEach(p => p.classList.toggle('hidden', p.id !== 'tab-' + name));
            history.replaceState(null, '', '#' + name);
        }

        btns.forEach(b => b.addEventListener('click', () => activate(b.dataset.tab)));

        // Restore from flash (after form save), then hash, then default
        const fromFlash = @json(session('active_tab', ''));
        const fromHash  = window.location.hash.replace('#', '');
        const valid     = ['chung', 'zalo', 'payment', 'cdn', 'ai', 'game', 'maintenance', 'system'];
        const initial   = valid.includes(fromFlash) ? fromFlash
                        : valid.includes(fromHash)  ? fromHash
                        : 'chung';
        activate(initial);
    })();

    function togglePassword(id) {
        const el = document.getElementById(id);
        if (el) el.type = el.type === 'password' ? 'text' : 'password';
    }

    function toggleTextarea(id) {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.style.webkitTextSecurity === 'disc' || el.dataset.masked === '1') {
            el.style.webkitTextSecurity = '';
            el.style.textSecurity = '';
            el.dataset.masked = '0';
        } else {
            el.style.webkitTextSecurity = 'disc';
            el.style.textSecurity = 'disc';
            el.dataset.masked = '1';
        }
    }
    </script>
</x-admin-layout>
