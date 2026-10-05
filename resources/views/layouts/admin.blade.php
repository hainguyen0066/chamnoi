<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ strip_tags($header ?? 'Quản trị') }} - {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-800" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            {{-- Mobile sidebar backdrop --}}
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-gray-900/60 lg:hidden"
                 style="display: none;"></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                   class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-xl">
                
                {{-- Logo / Brand Header --}}
                <div class="h-16 flex items-center justify-between px-6 bg-slate-950/60 border-b border-slate-800">
                    <a href="{{ route('admin.game-kicks.index') }}" class="flex items-center gap-2.5 font-bold text-white text-base tracking-wide hover:text-rose-400 transition">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-rose-600 to-red-500 flex items-center justify-center text-white shadow-md shadow-rose-500/30">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <span class="truncate">{{ config('app.name', 'GAME API PANEL') }}</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Navigation links: Chỉ hiển thị Kick người chơi --}}
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-4 text-sm">
                    <div>
                        <div class="px-3 mb-2 text-[11px] font-bold tracking-wider uppercase text-slate-400">Chức năng</div>
                        <div class="space-y-1">
                            {{-- Chức năng KICK NGƯỜI CHƠI --}}
                            <a href="{{ route('admin.game-kicks.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.game-kicks.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.game-kicks.*') ? 'text-white' : 'text-rose-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    <span>Kick người chơi</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded {{ request()->routeIs('admin.game-kicks.*') ? 'bg-white/20 text-white' : 'bg-rose-500/20 text-rose-300' }}">API v2</span>
                            </a>

                            {{-- Chức năng TRA CỨU NHÂN VẬT & TÀI KHOẢN --}}
                            <a href="{{ route('admin.game-lookups.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.game-lookups.*') ? 'bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.game-lookups.*') ? 'text-white' : 'text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                                    </svg>
                                    <span>Tra cứu nhân vật</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded {{ request()->routeIs('admin.game-lookups.*') ? 'bg-white/20 text-white' : 'bg-sky-500/20 text-sky-300' }}">API v2</span>
                            </a>

                            {{-- Quản lý TÀI KHOẢN (Admin & User) --}}
                            <a href="{{ route('admin.accounts.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.accounts.*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.accounts.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <span>Tài khoản</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded {{ request()->routeIs('admin.accounts.*') ? 'bg-white/20 text-white' : 'bg-indigo-500/20 text-indigo-300' }}">Admin/User</span>
                            </a>

                            {{-- Cài đặt BẢO MẬT 2FA --}}
                            <a href="{{ route('admin.two-factor.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.two-factor.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.two-factor.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    <span>Bảo mật 2FA</span>
                                </div>
                                @if (auth('admin')->user()?->hasTwoFactorEnabled())
                                    <span class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Bật</span>
                                @else
                                    <span class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">Chưa bật</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </nav>

                {{-- Admin Profile / Logout footer --}}
                <div class="p-3 border-t border-slate-800 bg-slate-950/40">
                    <div class="flex items-center justify-between px-2 py-1.5">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-semibold text-indigo-400 shrink-0">
                                {{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-white truncate">{{ auth('admin')->user()->name ?? 'Admin' }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ auth('admin')->user()->email ?? '' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" title="Đăng xuất" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main Layout Area --}}
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                {{-- Top Header --}}
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 shrink-0 shadow-sm z-10">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-lg font-bold text-gray-800 tracking-tight">{{ $header ?? 'Trang quản trị' }}</h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.game-kicks.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-lg text-xs font-semibold transition">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            Kick Player
                        </a>
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-gray-700">{{ auth('admin')->user()->name ?? 'Admin' }}</div>
                            <div class="text-[11px] text-gray-400">Administrator</div>
                        </div>
                    </div>
                </header>

                {{-- Page Main Content --}}
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                    @if (session('status'))
                        <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 font-medium">{{ session('status') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <div class="flex-1 font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <div class="mb-5 rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold mb-1">Vui lòng kiểm tra lại thông tin:</p>
                                <ul class="list-disc list-inside text-xs space-y-0.5 text-amber-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
