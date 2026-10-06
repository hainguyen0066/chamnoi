@extends('layouts.admin')

@section('title', 'Tổng quan Quản trị - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Tổng quan Hệ thống</h2>
            <p class="text-sm text-slate-500 mt-1">Theo dõi hoạt động của phụ huynh, bài viết và phiếu sàng lọc nguy cơ chậm nói.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Thêm bài viết mới</span>
            </a>
            <a href="{{ route('admin.screening-questions.create') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Thêm câu hỏi test</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Phiếu sàng lọc -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Phiếu Sàng Lọc</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    📋
                </div>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ $totalSubmissions }}</div>
            <div class="mt-2 text-xs text-slate-500 flex items-center gap-2">
                <span class="text-rose-600 font-bold">{{ $highRiskSubmissions }} nguy cơ cao</span>
                <span>•</span>
                <span class="text-amber-600 font-bold">{{ $mediumRiskSubmissions }} theo dõi</span>
            </div>
        </div>

        <!-- Card 2: Traffic Realtime -->
        <a href="{{ route('admin.traffic.index') }}" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-emerald-500 transition-all group block">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600">Lượt Xem Hôm Nay</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    📈
                </div>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ number_format($todayTrafficViews) }}</div>
            <div class="mt-2 text-xs text-slate-500">
                Tổng cộng <span class="font-bold text-emerald-600">{{ number_format($totalTrafficViews) }}</span> lượt xem toàn trang
            </div>
        </a>

        <!-- Card 3: Bài viết cẩm nang -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Bài Viết & Cẩm Nang</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm">
                    📚
                </div>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ $totalArticles }}</div>
            <div class="mt-2 text-xs text-slate-500">
                Gồm 6 chủ đề can thiệp
            </div>
        </div>

        <!-- Card 4: Bộ câu hỏi sàng lọc -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Bộ Câu Hỏi Mốc Chuẩn</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                    🎯
                </div>
            </div>
            <div class="text-3xl font-black text-slate-800">{{ $totalQuestions }}</div>
            <div class="mt-2 text-xs text-slate-500">
                Phân bổ cho 4 nhóm tuổi
            </div>
        </div>
    </div>

    <!-- Recent Submissions & Consultations Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Screening Submissions -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Phiếu sàng lọc gần đây</h3>
                    <p class="text-xs text-slate-400">Các bài test phụ huynh vừa thực hiện</p>
                </div>
                <a href="{{ route('admin.screening-submissions.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    Xem tất cả &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="p-3">Bé / Phụ huynh</th>
                            <th class="p-3">Nhóm tuổi</th>
                            <th class="p-3">Đánh giá nguy cơ</th>
                            <th class="p-3 text-right">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSubmissions as $sub)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3">
                                    <div class="font-bold text-slate-800">{{ $sub->child_name }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $sub->parent_name ?? 'Phụ huynh' }} - {{ $sub->parent_phone ?? 'Chưa để SĐT' }}</div>
                                </td>
                                <td class="p-3 font-medium text-slate-600">{{ $sub->age_group }}</td>
                                <td class="p-3">
                                    @if($sub->risk_level === 'high')
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">🚩 Nguy cơ cao</span>
                                    @elseif($sub->risk_level === 'medium')
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">⚠️ Theo dõi</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ Bình thường</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('admin.screening-submissions.show', $sub->id) }}" class="text-emerald-600 hover:underline font-bold">Xem</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">Chưa có phiếu sàng lọc nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Read Articles (SEO Traffic Leaders) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Top bài viết hút traffic nhiều nhất</h3>
                    <p class="text-xs text-slate-400">Các bài viết có lượt xem cao nhất từ Google & Mạng xã hội</p>
                </div>
                <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    Quản lý bài &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold">
                        <tr>
                            <th class="p-3">Tên bài viết</th>
                            <th class="p-3">Chủ đề</th>
                            <th class="p-3 text-center">Lượt xem</th>
                            <th class="p-3 text-right">Xem bài</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topArticles as $art)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3">
                                    <div class="font-bold text-slate-800 line-clamp-1">{{ $art->title }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $art->estimated_read_time }} đọc</div>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-medium text-[10px]">
                                        {{ $art->category }}
                                    </span>
                                </td>
                                <td class="p-3 text-center font-bold text-emerald-600">
                                    {{ number_format($art->views_count) }}
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="text-sky-600 hover:underline font-bold">
                                        Mở ↗
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">Chưa có bài viết nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
