@extends('layouts.app')

@section('title', 'Bài Trắc Nghiệm Sàng Lọc Chậm Nói Cho Trẻ - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16" x-data="{ currentAge: '{{ $selectedAge }}' }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Công cụ tự kiểm tra trực quan
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Bài Sàng Lọc Phát Triển Ngôn Ngữ
            </h1>
            <p class="text-slate-600 text-sm sm:text-base">
                Dựa trên thang đo mốc phát triển tâm vận động và giao tiếp sớm của CDC Hoa Kỳ. Giúp cha mẹ phát hiện sớm các dấu hiệu chậm nói hoặc cờ đỏ nguy cơ.
            </p>
        </div>

        <!-- Age Group Selector Tabs -->
        <div class="bg-white rounded-3xl p-3 shadow-sm border border-slate-200/80 mb-8 flex flex-wrap sm:flex-nowrap gap-2">
            @foreach($ageGroups as $key => $name)
                <a href="{{ route('screening.index', ['age' => $key]) }}"
                   class="flex-1 text-center py-3 px-3 rounded-2xl font-bold text-xs sm:text-sm transition duration-200 {{ $selectedAge === $key ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $name }}
                </a>
            @endforeach
        </div>

        <!-- Quiz Form -->
        <form method="POST" action="{{ route('screening.submit') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="age_group" value="{{ $selectedAge }}">

            <!-- Notice card -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
                <span class="text-xl">👶</span>
                <div>
                    <strong class="font-bold">Lưu ý khi quan sát bé:</strong>
                    <span>Hãy chọn câu trả lời dựa trên những hành vi bé thể hiện thường xuyên hàng ngày khi ở cạnh người thân quen, không tính những lần bé bị ốm, mệt hoặc cáu gắt nhé.</span>
                </div>
            </div>

            <!-- Question Cards -->
            <div class="space-y-6">
                @foreach($questions as $index => $q)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border {{ $q->is_red_flag ? 'border-amber-200/70 hover:border-amber-400' : 'border-slate-200 hover:border-emerald-300' }} transition-all">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </span>

                            @if($q->is_red_flag)
                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold uppercase tracking-wider shrink-0 flex items-center gap-1">
                                    <span>🚩</span> Dấu hiệu quan trọng
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading mb-2">
                            {{ $q->question }}
                        </h3>

                        @if($q->explanation)
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <strong class="text-slate-700">Giải thích:</strong> {{ $q->explanation }}
                            </p>
                        @endif

                        <!-- Radio Options -->
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="answers[{{ $q->id }}]" value="yes" required class="peer sr-only">
                                <div class="p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/70 transition flex items-center justify-center gap-2 text-center text-xs sm:text-sm font-bold text-slate-700 peer-checked:text-emerald-800 hover:bg-slate-50">
                                    <span class="text-emerald-600 font-black">✓</span>
                                    <span>Có / Bé làm tốt</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="answers[{{ $q->id }}]" value="no" required class="peer sr-only">
                                <div class="p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 peer-checked:border-rose-400 peer-checked:bg-rose-50/70 transition flex items-center justify-center gap-2 text-center text-xs sm:text-sm font-bold text-slate-700 peer-checked:text-rose-800 hover:bg-slate-50">
                                    <span class="text-rose-500 font-black">✗</span>
                                    <span>Chưa / Không làm được</span>
                                </div>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Parent Info Section -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 font-heading mb-2 flex items-center gap-2">
                    <span>📝</span> Thông tin của bé & gia đình (Không bắt buộc)
                </h3>
                <p class="text-xs text-slate-500 mb-6">Điền thông tin để kết quả được cá nhân hóa chính xác hơn theo tháng tuổi của bé.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tên của bé</label>
                        <input type="text" name="child_name" placeholder="Ví dụ: Bé Bắp" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tháng tuổi thực tế của bé (tháng)</label>
                        <input type="number" name="child_age_months" min="6" max="72" value="18" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Họ tên Phụ huynh</label>
                        <input type="text" name="parent_name" placeholder="Ví dụ: Mẹ Thảo My" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Số điện thoại / Zalo</label>
                        <input type="tel" name="parent_phone" placeholder="09xxxxxxxx" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center pt-4">
                <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-sky-600 hover:from-emerald-700 hover:to-sky-700 text-white font-bold text-base shadow-xl shadow-emerald-600/30 transition transform hover:-translate-y-0.5">
                    Xem Đánh Giá Kết Quả & Lời Khuyên Ngay &rarr;
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
