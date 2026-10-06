@extends('layouts.app')

@section('title', 'Tính Tuổi & Tra Cứu Mốc Phát Triển Ngôn Ngữ - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16" x-data="{
    birthDate: '',
    prematureWeeks: 0,
    calculatedAgeMonths: null,
    calculatedAgeText: '',
    selectedTab: '18m',
    calculateAge() {
        if (!this.birthDate) return;
        const birth = new Date(this.birthDate);
        const now = new Date();
        let diffMs = now - birth;
        if (this.prematureWeeks > 0) {
            diffMs -= (this.prematureWeeks * 7 * 24 * 60 * 60 * 1000);
        }
        const totalDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const months = Math.floor(totalDays / 30.4375);
        const days = Math.floor(totalDays % 30.4375);
        this.calculatedAgeMonths = months;
        this.calculatedAgeText = months + ' tháng ' + days + ' ngày';

        if (months < 12) this.selectedTab = '9m';
        else if (months < 18) this.selectedTab = '12m';
        else if (months < 24) this.selectedTab = '18m';
        else if (months < 36) this.selectedTab = '24m';
        else this.selectedTab = '36m';
    }
}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Tính tuổi & Mốc chuẩn phát triển</span>
        </nav>

        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Thước đo phát triển khoa học
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Mốc Chuẩn Ngôn Ngữ Theo Tháng Tuổi
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Nhập ngày sinh của bé để tra cứu nhanh các mốc giao tiếp bình thường và đối chiếu các dấu hiệu cờ đỏ cần can thiệp.
            </p>
        </div>

        <!-- Interactive Calculator Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-100 mb-12">
            <h3 class="text-lg font-bold text-slate-900 font-heading mb-4 flex items-center gap-2">
                <span>🧮</span> Công cụ tính tuổi chính xác của bé
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Ngày tháng năm sinh của bé <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" x-model="birthDate" @change="calculateAge()" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Sinh non (tuần) nếu có
                    </label>
                    <input type="number" x-model="prematureWeeks" @change="calculateAge()" min="0" max="16" placeholder="0" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Calculated Age Output -->
            <div x-show="calculatedAgeMonths !== null" x-cloak class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎉</span>
                    <div>
                        <div class="text-xs text-emerald-800 font-bold uppercase tracking-wider">Tuổi thực tế của con:</div>
                        <div class="text-lg font-black text-emerald-900 font-heading" x-text="calculatedAgeText"></div>
                    </div>
                </div>

                <a :href="'{{ route('screening.index') }}?age=' + (calculatedAgeMonths < 18 ? '12-18m' : (calculatedAgeMonths < 24 ? '18-24m' : (calculatedAgeMonths < 36 ? '2-3y' : '3-5y')))"
                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition text-center shrink-0">
                    Làm test sàng lọc đúng lứa tuổi này &rarr;
                </a>
            </div>
        </div>

        <!-- Milestones Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6">
            @foreach($ageMilestones as $key => $milestone)
                <button @click="selectedTab = '{{ $key }}'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition shrink-0"
                        :class="selectedTab === '{{ $key }}' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'">
                    {{ $milestone['title'] }}
                </button>
            @endforeach
        </div>

        <!-- Milestone Detail Panels -->
        @foreach($ageMilestones as $key => $milestone)
            <div x-show="selectedTab === '{{ $key }}'" x-cloak class="space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <h2 class="text-xl font-black text-slate-900 font-heading mb-6 flex items-center gap-2">
                        <span>🌟</span> {{ $milestone['title'] }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Speech milestone -->
                        <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2">
                                <span>🗣️</span> Phát triển ngôn ngữ chuẩn
                            </div>
                            <p class="text-sm text-slate-700 leading-relaxed font-medium">
                                {{ $milestone['speech'] }}
                            </p>
                        </div>

                        <!-- Social milestone -->
                        <div class="p-5 rounded-2xl bg-sky-50/60 border border-sky-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-sky-800 uppercase tracking-wider mb-2">
                                <span>🤝</span> Tương tác xã hội & Giao tiếp
                            </div>
                            <p class="text-sm text-slate-700 leading-relaxed font-medium">
                                {{ $milestone['social'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Red flags warnings -->
                    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200">
                        <div class="flex items-center gap-2 text-xs font-bold text-rose-800 uppercase tracking-wider mb-3">
                            <span>🚩</span> Dấu hiệu cờ đỏ cảnh báo nguy cơ ở độ tuổi này
                        </div>
                        <ul class="space-y-2 text-xs sm:text-sm text-rose-900 font-semibold">
                            @foreach($milestone['red_flags'] as $rf)
                                <li class="flex items-start gap-2">
                                    <span class="text-rose-500 font-black">•</span>
                                    <span>{{ $rf }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <span class="text-xs text-slate-500">Nếu con có từ 1 dấu hiệu cờ đỏ trở lên, hãy kiểm tra ngay:</span>
                        <a href="{{ route('screening.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                            Làm bài sàng lọc chi tiết
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection
