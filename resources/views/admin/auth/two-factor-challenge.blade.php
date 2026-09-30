<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xác thực 2 bước (2FA) - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md" x-data="{ useRecovery: false }">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-600 to-red-500 text-white shadow-lg shadow-rose-600/30 mb-3 text-2xl">
                🛡️
            </div>
            <h1 class="text-xl font-bold text-white tracking-wide">{{ config('app.name') }}</h1>
            <p class="text-xs text-slate-400 mt-1">Xác thực 2 bước bảo vệ tài khoản quản trị</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
            <div class="text-center mb-6">
                <h2 class="text-base font-bold text-slate-800" x-text="useRecovery ? 'Nhập Mã Khôi Phục Dự Phòng' : 'Nhập Mã Xác Thực (2FA)'"></h2>
                <p class="text-xs text-slate-500 mt-1" x-show="!useRecovery">
                    Mở ứng dụng <strong>Google Authenticator</strong> trên điện thoại và nhập mã 6 chữ số.
                </p>
                <p class="text-xs text-slate-500 mt-1" x-show="useRecovery" style="display: none;">
                    Nhập một trong các mã khôi phục dự phòng đã lưu khi thiết lập 2FA.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.2fa.challenge.submit') }}" class="space-y-5">
                @csrf

                {{-- Mode 1: TOTP 6-digit code --}}
                <div x-show="!useRecovery">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider text-center mb-2">
                        Mã Xác Thực 6 Số
                    </label>
                    <input type="text"
                           name="code"
                           autofocus
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           placeholder="000000"
                           class="w-full text-center tracking-[0.5em] text-2xl font-bold font-mono py-3 px-4 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-slate-50 focus:bg-white text-slate-800">
                </div>

                {{-- Mode 2: Recovery code --}}
                <div x-show="useRecovery" style="display: none;">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                        Mã Khôi Phục (Recovery Code)
                    </label>
                    <input type="text"
                           name="recovery_code"
                           placeholder="Nhập mã dự phòng..."
                           class="w-full font-mono text-xs py-3 px-4 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-slate-50 focus:bg-white text-slate-800">
                </div>

                <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold rounded-xl shadow-md shadow-rose-600/25 transition cursor-pointer text-sm">
                    Xác Nhận & Đăng Nhập
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <button type="button"
                        @click="useRecovery = !useRecovery"
                        class="text-indigo-600 hover:text-indigo-800 font-semibold underline underline-offset-2">
                    <span x-text="useRecovery ? '← Quay lại dùng mã 6 số' : 'Mất điện thoại? Dùng mã dự phòng'"></span>
                </button>

                <a href="{{ route('admin.login') }}" class="text-slate-400 hover:text-slate-600">
                    Đăng nhập lại
                </a>
            </div>
        </div>
    </div>
</body>
</html>
