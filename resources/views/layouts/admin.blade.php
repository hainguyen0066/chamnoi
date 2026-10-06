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
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-bold text-white text-base tracking-wide hover:text-emerald-400 transition">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                            🌱
                        </div>
                        <span class="truncate">Mầm Ngôn Ngữ</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Navigation links --}}
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-4 text-sm">
                    <div>
                        <div class="px-3 mb-2 text-[11px] font-bold tracking-wider uppercase text-slate-400">Chuyên môn & Dữ liệu</div>
                        <div class="space-y-1">
                            {{-- Dashboard --}}
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                    </svg>
                                    <span>Tổng quan</span>
                                </div>
                            </a>

                            {{-- Bài viết & Cẩm nang --}}
                            <a href="{{ route('admin.articles.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.articles.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    <span>Bài viết & Cẩm nang</span>
                                </div>
                            </a>

                            {{-- Bộ câu hỏi Sàng lọc --}}
                            <a href="{{ route('admin.screening-questions.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.screening-questions.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                    <span>Câu hỏi sàng lọc</span>
                                </div>
                            </a>

                            {{-- Phiếu kết quả Sàng lọc --}}
                            <a href="{{ route('admin.screening-submissions.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.screening-submissions.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <span>Phiếu sàng lọc</span>
                                </div>
                            </a>

                            {{-- Lộ trình 30 ngày --}}
                            <a href="{{ route('admin.roadmap.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.roadmap.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base shrink-0">📅</span>
                                    <span>Lộ trình 30 ngày</span>
                                </div>
                            </a>

                            {{-- Danh bạ Cơ sở y tế --}}
                            <a href="{{ route('admin.medical-centers.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.medical-centers.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base shrink-0">🏥</span>
                                    <span>Cơ sở y tế & BV</span>
                                </div>
                            </a>

                            {{-- Thống kê Traffic --}}
                            <a href="{{ route('admin.traffic.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.traffic.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base shrink-0">📊</span>
                                    <span>Thống kê Traffic</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400">SEO</span>
                            </a>

                            {{-- Treo Quảng Cáo & Kiếm Tiền --}}
                            <a href="{{ route('admin.ads.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.ads.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base shrink-0">💰</span>
                                    <span>Kiếm tiền & Ads</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400">AdSense</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="px-3 mb-2 text-[11px] font-bold tracking-wider uppercase text-slate-400">Hệ thống</div>
                        <div class="space-y-1">
                            {{-- Cấu hình Website --}}
                            <a href="{{ route('admin.settings.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Cài đặt chung</span>
                                </div>
                            </a>

                            {{-- Quản lý TÀI KHOẢN --}}
                            <a href="{{ route('admin.accounts.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.accounts.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <span>Tài khoản Admin</span>
                                </div>
                            </a>

                            {{-- Cài đặt BẢO MẬT 2FA --}}
                            <a href="{{ route('admin.two-factor.index') }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.two-factor.*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    <span>Bảo mật 2FA</span>
                                </div>
                                @if (auth('admin')->user()?->hasTwoFactorEnabled())
                                    <span class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Bật</span>
                                @else
                                    <span class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">Tắt</span>
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
                        <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-semibold transition shadow-sm">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            <span>Xem Website</span>
                        </a>
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-gray-700">{{ auth('admin')->user()->name ?? 'Admin' }}</div>
                            <div class="text-[11px] text-gray-400">Ban Chuyên Môn</div>
                        </div>
                    </div>
                </header>

                {{-- Page Main Content --}}
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                    @if (session('success') || session('status'))
                        <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 font-medium">{{ session('success') ?? session('status') }}</div>
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

                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
