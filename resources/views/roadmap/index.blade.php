@extends('layouts.app')

@section('title', 'Lộ Trình 30 Ngày "Cùng Con Bật Âm" - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16" x-data="{
    completedDays: JSON.parse(localStorage.getItem('speech_delay_completed_days') || '[]'),
    activeModalDay: null,
    toggleDay(dayNum) {
        if (this.completedDays.includes(dayNum)) {
            this.completedDays = this.completedDays.filter(d => d !== dayNum);
        } else {
            this.completedDays.push(dayNum);
        }
        localStorage.setItem('speech_delay_completed_days', JSON.stringify(this.completedDays));
    },
    isCompleted(dayNum) {
        return this.completedDays.includes(dayNum);
    },
    getProgressPercent() {
        return Math.round((this.completedDays.length / 30) * 100);
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Lộ trình 30 ngày cùng con bật âm</span>
        </nav>

        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Giáo trình can thiệp tại nhà
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Lộ Trình 30 Ngày "Cùng Con Bật Âm"
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Mỗi ngày chỉ cần <strong>20 - 30 phút</strong> tương tác tập trung 1-1 không thiết bị số. Thiết kế khoa học từ kích hoạt giao tiếp mắt, bật âm thanh tượng thanh đến bùng nổ vốn từ đơn và câu ghép 2 từ.
            </p>
        </div>

        <!-- Sticky Progress Bar Card -->
        <div class="sticky top-24 z-20 bg-white rounded-3xl p-5 sm:p-6 shadow-lg border border-emerald-100 mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-2xl">
                        🏆
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-heading">Tiến độ 30 ngày của mẹ và bé</h3>
                        <p class="text-xs text-slate-500">Đã hoàn thành <span class="font-bold text-emerald-600" x-text="completedDays.length"></span> / 30 ngày thử thách</p>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-2xl font-black text-emerald-600 font-heading" x-text="getProgressPercent() + '%'"></span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500" :style="'width: ' + getProgressPercent() + '%'"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 font-medium">
                <span>Tuần 1: Detox màn hình & Mắt</span>
                <span>Tuần 2: Âm tượng thanh</span>
                <span>Tuần 3: Vốn từ nhu cầu</span>
                <span>Tuần 4: Ghép từ đôi</span>
            </div>
        </div>

        <!-- Weeks & Days Grid -->
        <div class="space-y-12">
            @foreach($days as $weekNum => $weekDays)
                <div>
                    <!-- Week Header -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-sm flex items-center justify-center shadow-sm">
                            {{ $weekNum }}
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                            {{ $weeks[$weekNum] ?? 'Tuần ' . $weekNum }}
                        </h2>
                    </div>

                    <!-- Days Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($weekDays as $day)
                            <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border flex flex-col justify-between"
                                 :class="isCompleted({{ $day->day_number }}) ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200/90'">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">{{ $day->icon }}</span>
                                            <span class="font-black text-xs uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-full">
                                                Ngày {{ $day->day_number }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-slate-400 font-medium">⏱ {{ $day->duration_minutes }} phút</span>
                                    </div>

                                    <h3 class="text-base font-bold text-slate-900 font-heading mb-2 line-clamp-2">
                                        {{ $day->title }}
                                    </h3>

                                    <p class="text-xs text-slate-600 line-clamp-2 mb-4">
                                        <strong>Mục tiêu:</strong> {{ $day->goal }}
                                    </p>

                                    @if($day->target_words)
                                        <div class="mb-4">
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Từ khóa luyện tập:</span>
                                            <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-emerald-800 text-xs font-bold border border-slate-200">
                                                {{ $day->target_words }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <button @click="activeModalDay = {{ $day->toJson() }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                                        Xem hướng dẫn &rarr;
                                    </button>

                                    <button @click="toggleDay({{ $day->day_number }})"
                                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                                            :class="isCompleted({{ $day->day_number }}) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                        <span x-text="isCompleted({{ $day->day_number }}) ? '✓ Đã xong' : 'Chưa xong'"></span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Day Detail Modal -->
        <div x-show="activeModalDay" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="activeModalDay" @click="activeModalDay = null" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="activeModalDay" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full p-6 sm:p-8 border border-emerald-100">
                    <template x-if="activeModalDay">
                        <div class="space-y-5">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="text-3xl" x-text="activeModalDay.icon"></span>
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full" x-text="'Ngày ' + activeModalDay.day_number"></span>
                                        <h3 class="text-lg font-black text-slate-900 font-heading mt-1" x-text="activeModalDay.title"></h3>
                                    </div>
                                </div>
                                <button @click="activeModalDay = null" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Activity Name & Goal -->
                            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                                <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-1" x-text="'Trò chơi: ' + activeModalDay.activity_name"></div>
                                <p class="text-xs sm:text-sm text-slate-700 font-medium" x-text="'🎯 Mục tiêu: ' + activeModalDay.goal"></p>
                            </div>

                            <!-- Step by Step Instructions -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Cách mẹ chơi và trò chuyện cùng con</h4>
                                <div class="p-4 rounded-2xl bg-slate-50 text-slate-700 text-sm leading-relaxed border border-slate-100 whitespace-pre-line" x-text="activeModalDay.instructions"></div>
                            </div>

                            <!-- Target Words -->
                            <template x-if="activeModalDay.target_words">
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Từ khóa con sẽ phát âm</h4>
                                    <div class="px-4 py-2.5 rounded-xl bg-sky-50 text-sky-900 font-bold text-sm border border-sky-100 inline-block" x-text="activeModalDay.target_words"></div>
                                </div>
                            </template>

                            <!-- Parent Tip -->
                            <div class="p-4 rounded-2xl bg-amber-50 border-l-4 border-amber-500 text-amber-950 text-xs sm:text-sm">
                                <strong class="font-bold block mb-1">💡 Lời khuyên của chuyên viên:</strong>
                                <span x-text="activeModalDay.parent_tip"></span>
                            </div>

                            <!-- Complete button in modal -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <button @click="activeModalDay = null" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100">Đóng</button>
                                <button @click="toggleDay(activeModalDay.day_number); activeModalDay = null"
                                        class="px-5 py-2.5 rounded-xl font-bold text-xs shadow-md transition"
                                        :class="isCompleted(activeModalDay.day_number) ? 'bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-700 text-white'">
                                    <span x-text="isCompleted(activeModalDay.day_number) ? 'Hủy đánh dấu' : '✓ Đánh dấu đã hoàn thành'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
