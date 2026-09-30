<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h1>
                <p class="text-sm text-gray-500 mt-1">Khu vực quản trị — chỉ dành cho admin</p>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-6">Đăng nhập</h2>

                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username"
                               class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                        <input id="password" type="password" name="password" required
                               autocomplete="current-password"
                               class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-gray-300">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition">
                        Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
