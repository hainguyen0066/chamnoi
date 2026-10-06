@extends('layouts.app')

@section('title', 'Kết Quả Sàng Lọc Phát Triển Ngôn Ngữ - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Result Overview Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-200 mb-8 overflow-hidden relative">
            <div class="text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kết quả đánh giá cho bé {{ $submission->child_name }}</span>

                <!-- Dynamic Risk Badge -->
                @if($submission->risk_level === 'high')
                    <div class="w-20 h-20 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center text-4xl mx-auto mt-4 mb-4 shadow-inner">
                        🚩
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-full bg-rose-100 border border-rose-200 text-rose-800 font-black text-sm mb-3">
                        MỨC ĐỘ: NGUY CƠ CAO (CẦN KHÁM CHUYÊN KHOA SỚM)
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-4">
                        Bé Có Dấu Hiệu Chậm Nói Đáng Kể Hoặc Có Cờ Đỏ
                    </h1>
                @elseif($submission->risk_level === 'medium')
                    <div class="w-20 h-20 rounded-3xl bg-amber-100 text-amber-600 flex items-center justify-center text-4xl mx-auto mt-4 mb-4 shadow-inner">
                        ⚠️
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-full bg-amber-100 border border-amber-200 text-amber-800 font-black text-sm mb-3">
                        MỨC ĐỘ: CẦN THEO DÕI & TĂNG CƯỜNG TƯƠNG TÁC
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-4">
                        Bé Đang Chậm Hơn Mốc Phát Triển Một Vài Kỹ Năng
                    </h1>
                @else
                    <div class="w-20 h-20 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-4xl mx-auto mt-4 mb-4 shadow-inner">
                        🎉
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 font-black text-sm mb-3">
                        MỨC ĐỘ: PHÁT TRIỂN PHÙ HỢP THEO ĐỘ TUỔI
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-4">
                        Bé Đang Phát Triển Giao Tiếp Khá Tốt!
                    </h1>
                @endif

                <p class="text-slate-600 text-sm sm:text-base leading-relaxed bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-100 mb-6">
                    {{ $submission->advice_summary }}
                </p>

                <!-- Stats summary -->
                <div class="grid grid-cols-3 gap-3 max-w-md mx-auto text-center border-t border-slate-100 pt-6">
                    <div>
                        <div class="text-xs text-slate-400 font-medium">Nhóm tuổi</div>
                        <div class="text-base font-bold text-slate-800">{{ $submission->age_group }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-medium">Chỉ số cần chú ý</div>
                        <div class="text-base font-bold text-amber-600">{{ $submission->score }} / {{ $submission->total_questions }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-medium">Dấu hiệu cờ đỏ</div>
                        <div class="text-base font-bold text-rose-600">{{ $submission->red_flags_count }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Plan Section -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 mb-8">
            <h2 class="text-xl font-bold text-slate-900 font-heading mb-6 flex items-center gap-2">
                <span>🎯</span> Kế hoạch đồng hành tại nhà cho cha mẹ
            </h2>

            <div class="space-y-4">
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                    <h3 class="font-bold text-sm text-emerald-900 mb-1">1. Cai thiết bị điện tử triệt để trong 30 ngày</h3>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Cắt 100% tivi, điện thoại thông minh, iPad. Khi muốn nghe nhạc, chỉ mở loa phát âm thanh (podcast / nhạc thiếu nhi) không có hình ảnh chuyển động để bé không bị dán mắt vào màn hình.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-sky-50/60 border border-sky-100">
                    <h3 class="font-bold text-sm text-sky-900 mb-1">2. Áp dụng kỹ thuật 5 giây kiên nhẫn</h3>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Khi bé muốn ăn kẹo, uống nước hoặc lấy đồ chơi, không vội đưa ngay. Đưa món đồ gần miệng bạn, nói từ khóa 1-2 lần (ví dụ: "Nước... Uống") rồi nhìn vào mắt bé, đếm thầm 5 giây chờ bé bật âm đáp lại.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100">
                    <h3 class="font-bold text-sm text-amber-900 mb-1">3. Chơi trò chơi tương tác mặt đối mặt 30 phút/ngày</h3>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Chơi thổi bong bóng xà phòng, chơi ú oà, bắt chước tiếng kêu của các con vật (bò kêu ùm bò, chó sủa gâu gâu). Các âm thanh tượng thanh là bước đệm tốt nhất trước khi bé nói được từ ghép.
                    </p>
                </div>

                @if($submission->risk_level === 'high')
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900">
                        <h3 class="font-bold text-sm text-rose-800 mb-1">⚠️ Lời khuyên khám chuyên khoa:</h3>
                        <p class="text-xs sm:text-sm leading-relaxed">
                            Do bé có từ 2 cờ đỏ trở lên, cha mẹ nên đưa bé đến <strong>Bệnh viện Nhi Đồng (Khoa Tâm lý / Vật lý trị liệu - Âm ngữ)</strong> hoặc <strong>Viện Tai Mũi Họng</strong> để nội soi kiểm tra thính lực và đánh giá thang đo M-CHAT / Denver II toàn diện.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Answers Breakdown -->
        @if(is_array($submission->answers))
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 mb-8">
                <h2 class="text-xl font-bold text-slate-900 font-heading mb-4 flex items-center gap-2">
                    <span>📋</span> Chi tiết các câu hỏi đã trả lời
                </h2>

                <div class="divide-y divide-slate-100 text-sm">
                    @foreach($submission->answers as $ans)
                        <div class="py-3.5 flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $ans['question'] }}</p>
                                @if(!empty($ans['is_red_flag']))
                                    <span class="text-[10px] text-rose-600 font-bold">🚩 Cờ đỏ quan trọng</span>
                                @endif
                            </div>
                            <span class="shrink-0 px-3 py-1 rounded-xl text-xs font-bold {{ $ans['is_risk'] ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $ans['is_risk'] ? 'Chưa đạt' : 'Đạt chuẩn' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Action Links -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 print:hidden">
            <button onclick="window.print()" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm text-center shadow-md flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656h10.5z" />
                </svg>
                <span>In phiếu đánh giá (Print / PDF)</span>
            </button>
            <a href="{{ route('screening.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl border border-slate-300 hover:bg-slate-100 font-bold text-slate-700 text-sm text-center">
                Làm lại bài trắc nghiệm khác
            </a>
            <a href="{{ route('roadmap.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm text-center shadow-md">
                Bắt đầu lộ trình 30 ngày tại nhà
            </a>
            <a href="{{ route('medical-centers.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white text-rose-600 border border-rose-300 hover:bg-rose-50 font-bold text-sm text-center">
                Xem cơ sở y tế khám chuyên khoa
            </a>
        </div>

        <style>
            @media print {
                header, footer, nav, .print\:hidden {
                    display: none !important;
                }
                body {
                    background: white !important;
                    color: black !important;
                }
                .shadow-xl, .shadow-sm, .shadow-md {
                    box-shadow: none !important;
                    border: 1px solid #ddd !important;
                }
            }
        </style>

    </div>
</div>
@endsection
