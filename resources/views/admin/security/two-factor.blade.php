<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Bảo Mật Tài Khoản</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono font-semibold">2FA Google Authenticator</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if ($admin->hasTwoFactorEnabled())
            {{-- State: ĐÃ BẬT 2FA --}}
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-600/30">
                            🛡️
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mb-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Đang hoạt động
                            </div>
                            <h2 class="text-base font-bold text-slate-800">Xác Thực 2 Bước (2FA) Đã Được Kích Hoạt</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Tài khoản của bạn đang được bảo vệ an toàn. Mỗi lần đăng nhập sẽ yêu cầu mã từ Google Authenticator.</p>
                        </div>
                    </div>
                </div>

                {{-- Recovery Codes Card --}}
                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Mã Khôi Phục Dự Phòng (Recovery Codes)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Lưu các mã này ở nơi an toàn. Mỗi mã chỉ dùng được 1 lần để đăng nhập trong trường hợp bạn bị mất điện thoại hoặc không mở được ứng dụng Authenticator.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-slate-50 p-4 rounded-xl border border-slate-200 font-mono text-xs text-slate-700">
                        @foreach ($recoveryCodes as $code)
                            <div class="bg-white px-3 py-2 rounded-lg border border-slate-200 text-center font-semibold">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                        <form method="POST" action="{{ route('admin.two-factor.recovery-codes') }}">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Tạo mới mã dự phòng sẽ hủy toàn bộ các mã cũ trước đây. Bạn có chắc muốn tạo mới?')"
                                    class="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition border border-slate-200">
                                🔄 Tạo lại bộ mã dự phòng mới
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Danger Zone: Tắt 2FA --}}
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-rose-700 mb-1">Tắt Xác Thực 2FA</h3>
                    <p class="text-xs text-slate-500 mb-4">Khi tắt 2FA, tài khoản của bạn sẽ chỉ cần email và mật khẩu để đăng nhập (giảm độ bảo mật).</p>

                    <form method="POST" action="{{ route('admin.two-factor.disable') }}" class="flex flex-wrap items-center gap-3">
                        @csrf
                        <input type="password"
                               name="password"
                               required
                               placeholder="Nhập mật khẩu hiện tại..."
                               class="text-xs px-3.5 py-2.5 rounded-xl border-slate-300 bg-white focus:ring-rose-500 focus:border-rose-500 w-64">
                        <button type="submit"
                                onclick="return confirm('Bạn có chắc chắn muốn TẮT bảo mật 2FA?')"
                                class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            Tắt 2FA Ngay
                        </button>
                    </form>
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @else
            {{-- State: CHƯA BẬT 2FA (Thiết lập) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold">
                            🔐
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Kích Hoạt Bảo Mật 2 Bước (2FA)</h2>
                            <p class="text-xs text-slate-500">Bảo vệ trang quản trị chống bị hack mật khẩu hoặc dò quét từ bên ngoài</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Hướng dẫn 3 bước --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <span class="inline-flex w-6 h-6 rounded-full bg-slate-800 text-white items-center justify-center font-bold text-xs">1</span>
                            <div class="font-bold text-slate-800">Tải ứng dụng</div>
                            <p class="text-slate-500">Cài đặt <strong>Google Authenticator</strong> hoặc <strong>Microsoft Authenticator</strong> từ CH Play hoặc App Store trên điện thoại.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <span class="inline-flex w-6 h-6 rounded-full bg-slate-800 text-white items-center justify-center font-bold text-xs">2</span>
                            <div class="font-bold text-slate-800">Quét mã QR</div>
                            <p class="text-slate-500">Mở ứng dụng trên điện thoại, bấm dấu cộng <strong>(+)</strong> và chọn <strong>Quét mã QR</strong> bên dưới.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <span class="inline-flex w-6 h-6 rounded-full bg-slate-800 text-white items-center justify-center font-bold text-xs">3</span>
                            <div class="font-bold text-slate-800">Xác nhận mã</div>
                            <p class="text-slate-500">Nhập mã 6 chữ số vừa xuất hiện trên ứng dụng vào ô bên dưới để hoàn tất kích hoạt.</p>
                        </div>
                    </div>

                    {{-- QR Code & Secret Box --}}
                    <div class="flex flex-col sm:flex-row items-center gap-6 p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-indigo-50/30 border border-slate-200">
                        {{-- QR SVG --}}
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200 shrink-0">
                            {!! $qrCodeSvg !!}
                        </div>

                        <div class="space-y-3 text-xs w-full">
                            <div>
                                <span class="font-bold text-slate-700">Không quét được camera?</span>
                                <p class="text-slate-500 mt-0.5">Nhập mã khóa thiết lập thủ công này vào ứng dụng của bạn:</p>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-slate-200 font-mono text-xs font-bold text-indigo-700 select-all tracking-wider break-all shadow-2xs">
                                {{ $secret }}
                            </div>

                            <p class="text-[11px] text-slate-400">
                                💡 Loại khóa: <strong>Dựa trên thời gian (Time-based TOTP)</strong>.
                            </p>
                        </div>
                    </div>

                    {{-- Form nhập mã xác nhận 6 số --}}
                    <form method="POST" action="{{ route('admin.two-factor.enable') }}" class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4 shadow-sm">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nhập Mã 6 Chữ Số Từ Ứng Dụng Để Kích Hoạt <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="code"
                                   required
                                   maxlength="6"
                                   placeholder="Ví dụ: 123456"
                                   class="w-full sm:w-64 text-center tracking-[0.4em] font-mono text-xl font-bold py-2.5 px-4 rounded-xl border-slate-300 focus:ring-rose-500 focus:border-rose-500 bg-slate-50 focus:bg-white text-slate-800">
                            @error('code')
                                <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold rounded-xl shadow-md shadow-rose-600/25 transition text-xs cursor-pointer">
                            <span>Kích Hoạt 2FA Ngay</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
