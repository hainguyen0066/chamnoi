<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mầm Ngôn Ngữ - Cẩm Nang & Sàng Lọc Chậm Nói Ở Trẻ')</title>
    <meta name="description" content="@yield('meta_description', 'Cẩm nang toàn diện về chậm nói ở trẻ: nguyên nhân, bảng phân biệt tự kỷ vs chậm nói đơn thuần, cách can thiệp tại nhà và bài test sàng lọc trực quan cho cha mẹ.')">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="Mầm Ngôn Ngữ">
    <meta property="og:locale" content="vi_VN">

    @yield('meta_tags')

    @php
        $adsenseClientId = $globalSiteSettings['adsense_client_id'] ?? null;
        $adsenseAutoAds = ($globalSiteSettings['adsense_auto_ads_enabled'] ?? '0') === '1';
        $ga4Id = $globalSiteSettings['google_analytics_id'] ?? null;
    @endphp

    @if($ga4Id)
        <!-- Google Analytics 4 (GA4) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{ $ga4Id }}');
        </script>
    @endif

    @if($adsenseClientId && $adsenseAutoAds)
        <!-- Google AdSense Auto Ads -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}" crossorigin="anonymous"></script>
    @endif

    <!-- Fonts: Inter & Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, .font-heading {
            font-family: 'Quicksand', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-amber-50/30 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-emerald-500 selection:text-white" x-data="{ mobileMenu: false }">

    <!-- Top emergency banner -->
    <div class="bg-gradient-to-r from-teal-700 via-emerald-600 to-teal-800 text-white text-xs sm:text-sm py-2 px-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 truncate">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white/20 text-white text-[11px] font-bold">🌱</span>
                <span class="truncate font-medium">Giai đoạn vàng 0 - 3 tuổi: Can thiệp càng sớm, con càng nhanh bắt kịp bạn bè!</span>
            </div>
            <div class="flex items-center gap-4 shrink-0 font-medium text-xs">
                <span class="inline-flex items-center gap-1 text-emerald-200">
                    <span>Cổng thông tin phi lợi nhuận cho cha mẹ</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-3">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 shrink-0 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-500 to-sky-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition shrink-0">
                        <span class="text-xl sm:text-2xl">🌱</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-lg sm:text-xl font-black text-slate-800 tracking-tight font-heading flex items-center gap-1.5 whitespace-nowrap">
                            <span>Mầm Ngôn Ngữ</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 hidden sm:inline-block">Dành cho Cha Mẹ</span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium hidden 2xl:block truncate">Đồng hành khoa học cùng con bật âm & phát triển</p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2 font-semibold text-xs xl:text-sm text-slate-700 whitespace-nowrap">
                    <a href="{{ route('home') }}" class="px-2.5 xl:px-3 py-2 rounded-xl transition {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50 font-bold' : 'hover:text-emerald-600 hover:bg-slate-50' }}">
                        Trang chủ
                    </a>

                    <!-- Dropdown Chức Năng & Công Cụ Tự Đánh Giá -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @mouseleave="open = false">
                        <button @click="open = !open" @mouseenter="open = true" class="flex items-center gap-1 px-2.5 xl:px-3 py-2 rounded-xl transition hover:text-emerald-600 hover:bg-slate-50 {{ request()->routeIs('behavior-assessment.*', 'screening.*', 'vocabulary.*', 'milestones.*', 'flashcards.*', 'roadmap.*', 'medical-centers.*') ? 'text-emerald-700 bg-emerald-50 font-bold' : '' }}">
                            <span>Chức năng</span>
                            <span class="px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">7</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-emerald-600' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute left-0 top-full mt-2 w-80 rounded-2xl bg-white shadow-2xl border border-slate-100 p-2.5 z-50 divide-y divide-slate-100">
                            
                            <div class="pb-1.5 space-y-1">
                                <a href="{{ route('vocabulary.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-emerald-50 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-emerald-100/70 group-hover:scale-110 transition">🔤</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 flex items-center gap-1.5">
                                            <span>Kiểm tra vốn từ của con</span>
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-500 text-white text-[9px] font-extrabold uppercase">Viral</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 font-normal">Đo vốn từ đầu đời & vòng quay trò chơi</p>
                                    </div>
                                </a>

                                <a href="{{ route('behavior-assessment.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-rose-50 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-rose-100/70 group-hover:scale-110 transition">🚨</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-rose-700 flex items-center gap-1.5">
                                            <span>Phân tích hành vi & Cờ đỏ</span>
                                            <span class="px-1.5 py-0.5 rounded bg-rose-500 text-white text-[9px] font-extrabold uppercase">Hot</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 font-normal">Chấm điểm nguy cơ & dấu hiệu báo động</p>
                                    </div>
                                </a>

                                <a href="{{ route('screening.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-emerald-50 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-emerald-100/70 group-hover:scale-110 transition">📋</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Trắc nghiệm sàng lọc CDC</div>
                                        <p class="text-[11px] text-slate-400 font-normal">Bài test theo từng mốc 12m, 18m, 2y, 3y</p>
                                    </div>
                                </a>

                                <a href="{{ route('roadmap.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-teal-50 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-teal-100/70 group-hover:scale-110 transition">📅</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-teal-700">Lộ trình 30 ngày "Cùng con bật âm"</div>
                                        <p class="text-[11px] text-slate-400 font-normal">Mỗi ngày 1 bài tập & câu thần chú tương tác</p>
                                    </div>
                                </a>
                            </div>

                            <div class="pt-1.5 space-y-1">
                                <a href="{{ route('flashcards.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-amber-50 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-amber-100/70 group-hover:scale-110 transition">🎴</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-amber-700">Thẻ kích âm & Luyện cơ miệng</div>
                                        <p class="text-[11px] text-slate-400 font-normal">Nghe âm thanh mẫu & gợi ý khẩu hình</p>
                                    </div>
                                </a>

                                <a href="{{ route('milestones.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-sky-50 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-sky-100/70 group-hover:scale-110 transition">📏</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-sky-700">Tính tuổi & Mốc chuẩn phát triển</div>
                                        <p class="text-[11px] text-slate-400 font-normal">Tra cứu cột mốc ngôn ngữ theo tháng tuổi</p>
                                    </div>
                                </a>

                                <a href="{{ route('medical-centers.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-100 transition group">
                                    <span class="text-xl shrink-0 p-1.5 rounded-lg bg-slate-200/80 group-hover:scale-110 transition">🏥</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-slate-900">Danh bạ bệnh viện & Cơ sở y tế</div>
                                        <p class="text-[11px] text-slate-400 font-normal">Địa chỉ & chuyên khoa khám nhi uy tín</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('roadmap.index') }}" class="px-2.5 xl:px-3 py-2 rounded-xl transition {{ request()->routeIs('roadmap.*') ? 'text-emerald-700 bg-emerald-50 font-bold' : 'hover:text-emerald-600 hover:bg-slate-50' }}">
                        Lộ trình 30 ngày
                    </a>

                    <a href="{{ route('flashcards.index') }}" class="px-2.5 xl:px-3 py-2 rounded-xl transition {{ request()->routeIs('flashcards.*') ? 'text-emerald-700 bg-emerald-50 font-bold' : 'hover:text-emerald-600 hover:bg-slate-50' }}">
                        Thẻ kích âm
                    </a>

                    <a href="{{ route('articles.compare') }}" class="px-2.5 xl:px-3 py-2 rounded-xl transition {{ request()->routeIs('articles.compare') ? 'text-rose-700 bg-rose-50 font-bold' : 'hover:text-rose-600 hover:bg-slate-50' }}">
                        Phân biệt Tự Kỷ
                    </a>

                    <a href="{{ route('articles.index') }}" class="px-2.5 xl:px-3 py-2 rounded-xl transition {{ request()->routeIs('articles.index') ? 'text-emerald-700 bg-emerald-50 font-bold' : 'hover:text-emerald-600 hover:bg-slate-50' }}">
                        Cẩm nang
                    </a>
                </nav>

                <!-- Actions: CTA Buttons to Tools -->
                <div class="hidden sm:flex items-center gap-2 shrink-0">
                    <a href="{{ route('screening.index') }}" class="px-3 xl:px-3.5 py-2 rounded-xl border border-emerald-300 text-emerald-800 hover:bg-emerald-50 font-bold text-xs transition inline-flex items-center gap-1.5 whitespace-nowrap">
                        <span>📋</span>
                        <span>Trắc Nghiệm CDC</span>
                    </a>
                    <a href="{{ route('behavior-assessment.index') }}" class="px-3.5 xl:px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-bold text-xs xl:text-sm shadow-md shadow-rose-500/20 hover:shadow-lg transition flex items-center gap-1.5 whitespace-nowrap">
                        <span>🚨</span>
                        <span>Phân Tích Cờ Đỏ</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 lg:hidden shrink-0">
                    <a href="{{ route('behavior-assessment.index') }}" class="px-2.5 py-1.5 rounded-lg bg-rose-600 text-white font-bold text-xs whitespace-nowrap">
                        Đánh giá cờ đỏ
                    </a>
                    <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenu" x-cloak x-transition class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2 shadow-xl">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Trang chủ</a>
            <a href="{{ route('vocabulary.index') }}" class="block px-3 py-2.5 rounded-xl font-bold bg-emerald-50 text-emerald-800">🔤 Kiểm tra vốn từ của bé & Vòng quay</a>
            <a href="{{ route('behavior-assessment.index') }}" class="block px-3 py-2.5 rounded-xl font-bold bg-rose-50 text-rose-700">🚨 Phân tích hành vi & Cờ đỏ y tế</a>
            <a href="{{ route('screening.index') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-emerald-700 hover:bg-emerald-50">Trắc nghiệm mốc tuổi CDC</a>
            <a href="{{ route('roadmap.index') }}" class="block px-3 py-2.5 rounded-xl font-bold text-emerald-800 hover:bg-emerald-50">📅 Lộ trình 30 ngày "Cùng con bật âm"</a>
            <a href="{{ route('articles.compare') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-amber-700 hover:bg-amber-50">Phân biệt Chậm nói vs Tự Kỷ</a>
            <a href="{{ route('milestones.index') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:bg-emerald-50">Tính tuổi & Mốc chuẩn phát triển</a>
            <a href="{{ route('flashcards.index') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:bg-emerald-50">Thẻ kích âm & Luyện cơ miệng</a>
            <a href="{{ route('medical-centers.index') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:bg-emerald-50">Danh bạ bệnh viện & Phòng khám</a>
            <a href="{{ route('articles.index') }}" class="block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:bg-emerald-50">Cẩm nang kiến thức</a>
        </div>
    </header>

    <!-- Success flash banner -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-2xl bg-emerald-500 text-white p-4 shadow-lg flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎉</span>
                    <p class="font-medium text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">&times;</button>
            </div>
        </div>
    @endif

    <!-- Main Content Slot -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <!-- Col 1: About -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white text-xl">🌱</div>
                        <span class="text-xl font-black text-white font-heading">Mầm Ngôn Ngữ</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Cổng thông tin phi lợi nhuận hướng dẫn cha mẹ nhận diện, thấu hiểu và đồng hành khoa học cùng trẻ chậm nói trong giai đoạn cửa sổ vàng.
                    </p>
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800 text-emerald-400 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Nền tảng phi lợi nhuận vì cộng đồng
                        </span>
                    </div>
                </div>

                <!-- Col 2: Chủ đề chính -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 font-heading">Chủ đề cha mẹ quan tâm</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('articles.compare') }}" class="hover:text-emerald-400 transition flex items-center gap-1.5">👉 Phân biệt Chậm nói vs Tự kỷ</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'nguyen-nhan']) }}" class="hover:text-emerald-400 transition">Tác hại của Smartphone & Tivi</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'cach-xu-ly']) }}" class="hover:text-emerald-400 transition">Quy tắc 3T & Kỹ thuật 5s chờ đợi</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'tro-choi']) }}" class="hover:text-emerald-400 transition">Trò chơi kích âm tại nhà</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'co-do']) }}" class="hover:text-emerald-400 transition text-rose-400">Dấu hiệu cờ đỏ cần đi khám ngay</a></li>
                    </ul>
                </div>

                <!-- Col 3: Công cụ & Tiện ích -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 font-heading">Công cụ hỗ trợ</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('screening.index', ['age' => '12-18m']) }}" class="hover:text-emerald-400 transition">Sàng lọc trẻ 12 - 18 tháng</a></li>
                        <li><a href="{{ route('screening.index', ['age' => '18-24m']) }}" class="hover:text-emerald-400 transition">Sàng lọc trẻ 18 - 24 tháng</a></li>
                        <li><a href="{{ route('screening.index', ['age' => '2-3y']) }}" class="hover:text-emerald-400 transition">Sàng lọc trẻ 2 - 3 tuổi</a></li>
                        <li><a href="{{ route('screening.index', ['age' => '3-5y']) }}" class="hover:text-emerald-400 transition">Sàng lọc trẻ 3 - 5 tuổi</a></li>
                    </ul>
                </div>

                <!-- Col 4: Cảnh báo y khoa -->
                <div>
                    <h4 class="text-white font-bold text-base mb-4 font-heading">Miễn trừ trách nhiệm</h4>
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700 text-xs text-slate-400 leading-relaxed">
                        <strong class="text-amber-300 block mb-1">⚠️ Lưu ý quan trọng:</strong>
                        Các bài test sàng lọc và nội dung trên website được tổng hợp theo tài liệu CDC và hiệp hội Âm ngữ, chỉ có giá trị tham khảo định hướng. Khi có nghi ngờ, cha mẹ hãy đưa bé đến bệnh viện chuyên khoa Nhi để được chẩn đoán toàn diện.
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Mầm Ngôn Ngữ. Phát triển vì nụ cười và tiếng nói đầu đời của trẻ thơ.</p>
            </div>
        </div>
    <!-- Global Realtime Click & Event Tracking Script -->
    <script>
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a, button, [data-track]');
            if (!target) return;

            let eventName = target.getAttribute('data-track');
            let eventLabel = target.getAttribute('data-track-label') || target.innerText?.trim()?.slice(0, 100);

            const href = target.getAttribute('href') || '';
            if (!eventName) {
                if (href.includes('shopee.vn') || href.includes('lazada.vn')) {
                    eventName = 'affiliate_click';
                    eventLabel = 'Shopee / Affiliate: ' + (eventLabel || 'Product Link');
                } else if (href.includes('facebook.com/sharer') || href.includes('zalo.me')) {
                    eventName = 'social_share';
                    eventLabel = 'Chia sẻ Mạng xã hội';
                } else if (target.innerText?.includes('giọng mẫu') || target.innerText?.includes('âm thanh')) {
                    eventName = 'play_audio';
                    eventLabel = 'Nghe Audio Kích Âm';
                } else if (target.innerText?.includes('Trắc Nghiệm') || target.innerText?.includes('Sàng Lọc') || target.innerText?.includes('Phân Tích Cờ Đỏ')) {
                    eventName = 'click_test';
                    eventLabel = target.innerText?.trim()?.slice(0, 50);
                } else if (target.innerText?.includes('Quay Trò Chơi')) {
                    eventName = 'spin_wheel';
                    eventLabel = 'Vòng quay trò chơi';
                }
            }

            if (eventName) {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch('{{ route("track.click") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({
                        event_name: eventName,
                        event_label: eventLabel,
                        page_url: window.location.href
                    }),
                    keepalive: true
                }).catch(() => {});
            }
        }, true);
    </script>
</body>
</html>
