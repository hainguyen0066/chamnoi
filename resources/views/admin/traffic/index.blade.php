@extends('layouts.admin')

@section('title', 'Thống Kê Traffic & Lượt Xem - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <span>📊</span>
                <span>Thống Kê Traffic & Phân Tích Lượt Xem</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Theo dõi thời gian thực số lượng phụ huynh truy cập, nguồn traffic từ Google/Facebook và bài viết hot nhất.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Hệ thống ghi nhận Realtime</span>
            </span>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tổng Lượt Xem</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">👁️</span>
            </div>
            <div class="text-3xl font-black text-slate-900 font-heading mt-2">{{ number_format($totalPageviews) }}</div>
            <div class="text-xs text-slate-500 mt-1">Toàn bộ trang trên website</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Hôm Nay</span>
                <span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg">📅</span>
            </div>
            <div class="text-3xl font-black text-sky-600 font-heading mt-2">{{ number_format($todayPageviews) }}</div>
            <div class="text-xs text-slate-500 mt-1">Hôm qua: {{ number_format($yesterdayPageviews) }} lượt xem</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Khách Duy Nhất (IP)</span>
                <span class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg">👥</span>
            </div>
            <div class="text-3xl font-black text-purple-600 font-heading mt-2">{{ number_format($totalUniqueVisitors) }}</div>
            <div class="text-xs text-slate-500 mt-1">Hôm nay: {{ number_format($todayUniqueVisitors) }} người mới</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tỷ Lệ Mobile</span>
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">📱</span>
            </div>
            <div class="text-3xl font-black text-amber-600 font-heading mt-2">
                {{ round(($mobileCount / $totalDevices) * 100) }}%
            </div>
            <div class="text-xs text-slate-500 mt-1">{{ number_format($mobileCount) }} lượt từ điện thoại</div>
        </div>
    </div>

    <!-- 7-Day Chart & Device Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 7-Day Traffic Chart (Col 1 & 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Xu Hướng Lượt Xem 7 Ngày Gần Nhất</h3>
                    <p class="text-xs text-slate-400">Đo lường tăng trưởng traffic tự nhiên</p>
                </div>
                <span class="text-xs text-slate-500 font-medium bg-slate-100 px-3 py-1 rounded-lg">7 ngày qua</span>
            </div>

            <!-- Visual Bar Chart -->
            <div class="h-56 flex items-end justify-between gap-3 pt-8 pb-2 border-b border-slate-100">
                @foreach($dailyStats as $stat)
                    @php
                        $heightPercent = $maxDailyCount > 0 ? max(10, round(($stat['count'] / $maxDailyCount) * 100)) : 10;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 group relative">
                        <!-- Tooltip on hover -->
                        <div class="opacity-0 group-hover:opacity-100 transition absolute -top-8 px-2 py-1 rounded bg-slate-900 text-white text-[10px] font-bold whitespace-nowrap z-10 pointer-events-none">
                            {{ $stat['count'] }} lượt xem
                        </div>

                        <!-- Bar -->
                        <div class="w-full bg-slate-100 rounded-t-xl overflow-hidden h-40 flex items-end">
                            <div class="w-full bg-gradient-to-t from-emerald-600 to-teal-400 rounded-t-xl transition-all duration-500 group-hover:from-emerald-500 group-hover:to-teal-300"
                                 style="height: {{ $heightPercent }}%;"></div>
                        </div>

                        <!-- Date label -->
                        <span class="text-[11px] font-bold text-slate-600">{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between text-xs text-slate-400 pt-3">
                <span>Hôm qua: {{ $dailyStats[5]['count'] ?? 0 }} views</span>
                <span class="font-bold text-emerald-700">Hôm nay: {{ $dailyStats[6]['count'] ?? 0 }} views</span>
            </div>
        </div>

        <!-- Devices & Referrers (Col 3) -->
        <div class="space-y-6">
            <!-- Device Breakdown -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="font-bold text-sm text-slate-800 mb-4">Thiết Bị Người Dùng</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5">📱 Mobile / Smartphone</span>
                            <span>{{ round(($mobileCount / $totalDevices) * 100) }}% ({{ $mobileCount }})</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ ($mobileCount / $totalDevices) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5">💻 Máy Tính / Laptop</span>
                            <span>{{ round(($desktopCount / $totalDevices) * 100) }}% ({{ $desktopCount }})</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-sky-500 h-full rounded-full" style="width: {{ ($desktopCount / $totalDevices) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5">📟 Máy tính bảng / Tablet</span>
                            <span>{{ round(($tabletCount / $totalDevices) * 100) }}% ({{ $tabletCount }})</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full rounded-full" style="width: {{ ($tabletCount / $totalDevices) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Traffic Sources -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="font-bold text-sm text-slate-800 mb-3">Nguồn Giới Thiệu (Referrer)</h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($referrers as $ref)
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="font-medium text-slate-700 flex items-center gap-2">
                                @if(str_contains(strtolower($ref->referrer_domain), 'google')) 🔍
                                @elseif(str_contains(strtolower($ref->referrer_domain), 'facebook')) 🌐
                                @elseif(str_contains(strtolower($ref->referrer_domain), 'zalo')) 💬
                                @elseif(str_contains(strtolower($ref->referrer_domain), 'direct')) 🔗
                                @else 🌍 @endif
                                <span>{{ $ref->referrer_domain }}</span>
                            </span>
                            <span class="font-bold text-slate-900">{{ number_format($ref->total) }}</span>
                        </div>
                    @empty
                        <p class="py-2 text-slate-400">Đang thu thập dữ liệu nguồn truy cập...</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Top SEO Articles & High-Traffic Pages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Articles -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-slate-800">Top Bài Viết Nhiều Lượt Xem Nhất (SEO Ranking)</h3>
                <a href="{{ route('admin.articles.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Quản lý bài viết &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($topArticles as $index => $art)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 truncate">
                            <span class="w-6 h-6 rounded-lg {{ $index < 3 ? 'bg-amber-100 text-amber-800 font-black' : 'bg-slate-100 text-slate-600 font-bold' }} text-xs flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="truncate">
                                <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="font-bold text-xs sm:text-sm text-slate-800 hover:text-emerald-600 transition truncate block">
                                    {{ $art->title }}
                                </a>
                                <span class="text-[11px] text-slate-400">{{ $art->category_label }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-black text-xs">
                                {{ number_format($art->views_count) }} views
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top URLs -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="font-bold text-sm text-slate-800 mb-4">Top Trang Được Phụ Huynh Xem Nhiều Nhất</h3>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($topUrls as $u)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <span class="font-mono text-slate-700 truncate max-w-sm">{{ $u->url }}</span>
                        <span class="font-bold text-slate-900 shrink-0">{{ number_format($u->visits) }} lượt</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- PHÂN TÍCH LƯỢT CLICK VÀ HÀNH VI TƯƠNG TÁC (CLICK CONVERSION TRACKING) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>🎯</span>
                    <span>Phân Tích Lượt Click Vào Nút Bấm & Liên Kết Kiếm Tiền</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Đo lường mức độ tương tác thực tế của người dùng: Click mua sách, nghe âm thanh, làm bài test</p>
            </div>
            <div class="text-xs text-slate-500 font-bold bg-amber-50 text-amber-800 px-3 py-1.5 rounded-xl border border-amber-200/60 shrink-0">
                Tổng cộng: {{ number_format($totalClicks) }} lượt click
            </div>
        </div>

        <!-- 4 Click KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
                <div class="flex items-center justify-between text-xs font-bold text-amber-800 mb-1">
                    <span>🛒 Click Mua Shopee</span>
                    <span class="text-base">💰</span>
                </div>
                <div class="text-2xl font-black text-amber-900">{{ number_format($affiliateClicks) }}</div>
                <div class="text-[11px] text-amber-700 mt-0.5">Tiềm năng hoa hồng Affiliate</div>
            </div>

            <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200">
                <div class="flex items-center justify-between text-xs font-bold text-teal-800 mb-1">
                    <span>🔊 Click Nghe Thẻ Âm</span>
                    <span class="text-base">🎴</span>
                </div>
                <div class="text-2xl font-black text-teal-900">{{ number_format($audioClicks) }}</div>
                <div class="text-[11px] text-teal-700 mt-0.5">Nghe phát âm con vật & xe cộ</div>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                <div class="flex items-center justify-between text-xs font-bold text-emerald-800 mb-1">
                    <span>📝 Click Bắt Đầu Test</span>
                    <span class="text-base">📋</span>
                </div>
                <div class="text-2xl font-black text-emerald-900">{{ number_format($testClicks) }}</div>
                <div class="text-[11px] text-emerald-700 mt-0.5">Sàng lọc CDC & Cờ đỏ y tế</div>
            </div>

            <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200">
                <div class="flex items-center justify-between text-xs font-bold text-sky-800 mb-1">
                    <span>📢 Click Chia Sẻ MXH</span>
                    <span class="text-base">🌐</span>
                </div>
                <div class="text-2xl font-black text-sky-900">{{ number_format($shareClicks) }}</div>
                <div class="text-[11px] text-sky-700 mt-0.5">Chia sẻ Facebook & Zalo</div>
            </div>
        </div>

        <!-- Top Clicked Elements Table -->
        <div>
            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400 mb-3">Top Vị Trí / Nút Bấm Được Phụ Huynh Bấm Nhiều Nhất</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold border-y border-slate-100">
                        <tr>
                            <th class="p-3">Tên nút bấm / Nhãn sự kiện</th>
                            <th class="p-3">Loại hành vi</th>
                            <th class="p-3 text-right">Số lượt click</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topClickEvents as $ev)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-semibold text-slate-800">
                                    {{ $ev->event_label ?? 'Không xác định' }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] 
                                        {{ $ev->event_name === 'affiliate_click' ? 'bg-amber-100 text-amber-800' : 
                                           ($ev->event_name === 'play_audio' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $ev->event_name }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-black text-slate-900">
                                    {{ number_format($ev->total) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-slate-400">
                                    Chưa có lượt click nào được ghi nhận. Dữ liệu sẽ xuất hiện khi phụ huynh tương tác trên website.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- LIVE REALTIME TRAFFIC FEED (NHẬT KÝ LƯỢT TRUY CẬP GẦN NHẤT) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Nhật Ký Lượt Truy Cập Thời Gian Thực (15 Lượt Gần Nhất)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Xem trực tiếp người dùng đang xem trang nào và đến từ nguồn nào</p>
            </div>
            <span class="text-xs font-mono text-slate-400">{{ now()->format('H:i:s d/m/Y') }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-y border-slate-100">
                    <tr>
                        <th class="p-3">Thời gian</th>
                        <th class="p-3">Trang truy cập (URL)</th>
                        <th class="p-3">Thiết bị</th>
                        <th class="p-3">Nguồn giới thiệu (Referrer)</th>
                        <th class="p-3 text-right">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @forelse($recentVisits as $rv)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 text-slate-500 font-sans whitespace-nowrap">
                                {{ $rv->created_at->diffForHumans() }}
                            </td>
                            <td class="p-3 font-semibold text-slate-800 max-w-xs truncate" title="{{ $rv->url }}">
                                {{ $rv->url }}
                            </td>
                            <td class="p-3 font-sans whitespace-nowrap">
                                @if($rv->device_type === 'mobile')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">📱 Mobile</span>
                                @elseif($rv->device_type === 'tablet')
                                    <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 font-bold text-[10px]">📟 Tablet</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 font-bold text-[10px]">💻 Desktop</span>
                                @endif
                            </td>
                            <td class="p-3 font-sans whitespace-nowrap">
                                <span class="font-medium text-slate-700">
                                    {{ $rv->referrer_domain ?? 'Direct' }}
                                </span>
                            </td>
                            <td class="p-3 text-right text-slate-400">
                                {{ Str::mask($rv->ip_address ?? '127.0.0.1', '*', -3) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400 font-sans">
                                Chưa có nhật ký truy cập nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
