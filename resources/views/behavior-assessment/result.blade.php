@extends('layouts.app')

@section('title', 'Báo Cáo Phân Tích Hành Vi Của Bé ' . $submission->child_name . ' - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-gradient-to-b from-slate-50 via-white to-slate-100 py-10 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Actions Bar: Print & Share -->
        <div class="flex items-center justify-between gap-4 mb-6 print:hidden">
            <a href="{{ route('behavior-assessment.index') }}" class="text-xs sm:text-sm font-bold text-slate-500 hover:text-emerald-600 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Làm bài đánh giá khác</span>
            </a>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.036-.34-2.13-.34-3.235 0-3.666 2.012-6.84 5.02-8.39a.75.75 0 011.08.685v3.136a.75.75 0 00.75.75h3.136a.75.75 0 01.685 1.08A9.742 9.742 0 0117.62 10.594M6.72 13.829a9.71 9.71 0 003.58 3.58m-3.58-3.58l-2.02 2.02a.75.75 0 00-.22.53v3.75c0 .414.336.75.75.75h3.75a.75.75 0 00.53-.22l2.02-2.02m5.02-4.04a9.742 9.742 0 013.58-3.58m-3.58 3.58l2.02 2.02a.75.75 0 00.53.22h3.75a.75.75 0 00.75-.75v-3.75a.75.75 0 00-.22-.53l-2.02-2.02" />
                    </svg>
                    <span>In Phiếu Đánh Giá (Print / PDF)</span>
                </button>
            </div>
        </div>

        <!-- MEDICAL DISCLAIMER BANNER (MANDATORY & VISIBLE BOTH ON SCREEN & PRINT) -->
        <div class="mb-8 p-4 sm:p-5 rounded-3xl bg-amber-500/10 border-2 border-amber-300 text-amber-950 flex items-start gap-3 sm:gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0">
                🛡️
            </div>
            <div class="text-xs sm:text-sm leading-relaxed">
                <strong class="font-bold text-amber-900 block text-sm sm:text-base mb-0.5">
                    LƯU Ý QUAN TRỌNG: ỨNG DỤNG CHỈ LÀ MỘT CÁCH THAM KHẢO BAN ĐẦU
                </strong>
                <span>
                    Bảng phân tích hành vi và thang điểm dưới đây được xây dựng dựa trên các tiêu chí y khoa quốc tế (M-CHAT-R, CDC) nhằm giúp phụ huynh <strong>QUAN SÁT CÓ HỆ THỐNG VÀ PHÁT HIỆN SỚM CỜ ĐỎ</strong>. Kết quả này <strong>KHÔNG PHẢI VÀ KHÔNG THAY THẾ CHẨN ĐOÁN LÂM SÀNG CỦA BÁC SĨ</strong>. Cha mẹ không nên tự kết luận hay gán nhãn cho con, hãy luôn đưa bé đến bệnh viện chuyên khoa để được thăm khám toàn diện.
                </span>
            </div>
        </div>

        <!-- Main Score Overview Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-200 mb-8 overflow-hidden relative">
            <div class="text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Báo cáo phân tích hành vi của bé <strong class="text-slate-700">{{ $submission->child_name }}</strong> ({{ $submission->child_age_months }} tháng tuổi)
                </span>

                <!-- Dynamic Level Badge -->
                @if($submission->risk_level === 'high')
                    <div class="w-20 h-20 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center text-4xl mx-auto mt-4 mb-3 shadow-inner animate-bounce">
                        🚨
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-full bg-rose-100 border border-rose-200 text-rose-800 font-black text-xs sm:text-sm mb-3">
                        MỨC ĐỘ: BÁO ĐỘNG ĐỎ - NGUY CƠ CAO
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-3">
                        Bé Có Các Dấu Hiệu Báo Động Cần Chú Ý Đặc Biệt
                    </h1>
                @elseif($submission->risk_level === 'medium')
                    <div class="w-20 h-20 rounded-3xl bg-amber-100 text-amber-600 flex items-center justify-center text-4xl mx-auto mt-4 mb-3 shadow-inner">
                        ⚠️
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-full bg-amber-100 border border-amber-200 text-amber-800 font-black text-xs sm:text-sm mb-3">
                        MỨC ĐỘ: CẢNH BÁO TRUNG BÌNH - CẦN THEO DÕI SÁT
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-3">
                        Bé Đang Chậm Hơn Mốc Tuổi Ở Một Số Kỹ Năng Tương Tác
                    </h1>
                @else
                    <div class="w-20 h-20 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-4xl mx-auto mt-4 mb-3 shadow-inner">
                        🎉
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 font-black text-xs sm:text-sm mb-3">
                        MỨC ĐỘ: NGUY CƠ THẤP - TƯƠNG TÁC PHÙ HỢP
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-3">
                        Hành Vi & Tương Tác Của Bé Trong Giới Hạn Bình Thường
                    </h1>
                @endif

                <!-- Clinical Impression Box -->
                @if($submission->clinical_impression)
                    <div class="p-3.5 sm:p-4 rounded-2xl {{ $submission->risk_level === 'high' ? 'bg-rose-50 text-rose-900 border border-rose-200' : ($submission->risk_level === 'medium' ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-200') }} font-bold text-sm mb-4">
                        <span>🔍 Hướng phân loại gợi ý: </span>
                        <span>{{ $submission->clinical_impression }}</span>
                    </div>
                @endif

                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6">
                    {{ $submission->advice_summary }}
                </p>

                <!-- Statistics Metrics Grid -->
                <div class="grid grid-cols-3 gap-3 max-w-lg mx-auto text-center border-t border-slate-100 pt-6">
                    <div class="bg-slate-50 p-3 rounded-2xl">
                        <div class="text-[11px] text-slate-400 font-medium">Thang đánh giá</div>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $formInfo['title'] }}</div>
                    </div>
                    <div class="bg-amber-50/70 p-3 rounded-2xl border border-amber-100">
                        <div class="text-[11px] text-amber-700 font-medium">Chỉ số rủi ro</div>
                        <div class="text-base sm:text-lg font-black text-amber-600">{{ $submission->score }} / {{ $submission->total_questions }}</div>
                    </div>
                    <div class="bg-rose-50/70 p-3 rounded-2xl border border-rose-100">
                        <div class="text-[11px] text-rose-700 font-medium">Báo động đỏ</div>
                        <div class="text-base sm:text-lg font-black text-rose-600">{{ $submission->red_flags_count }} cờ đỏ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOCTOR VISIT RECOMMENDATION: CÓ CẦN ĐI KHÁM BÁC SĨ KHÔNG? -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-lg border-2 {{ $submission->need_doctor ? 'border-rose-400 bg-gradient-to-br from-rose-50/40 via-white to-rose-50/20' : 'border-emerald-300' }} mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-10 h-10 rounded-2xl {{ $submission->need_doctor ? 'bg-rose-500' : 'bg-emerald-500' }} text-white flex items-center justify-center text-xl shrink-0 shadow-md">
                    👨‍⚕️
                </span>
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 font-heading">
                        Câu Hỏi Quan Trọng: Bé Có Cần Đi Khám Bác Sĩ Không?
                    </h2>
                    <span class="text-xs text-slate-500">Chỉ định định hướng dựa trên số lượng cờ đỏ và điểm rủi ro hành vi</span>
                </div>
            </div>

            <!-- Direct Verdict Box -->
            <div class="p-4 sm:p-5 rounded-2xl {{ $submission->need_doctor ? 'bg-rose-600 text-white' : ($submission->risk_level === 'medium' ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white') }} mb-6 shadow-md">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ $submission->need_doctor ? '🚨' : ($submission->risk_level === 'medium' ? '⚠️' : '✅') }}</span>
                    <div>
                        <div class="text-xs uppercase tracking-wider opacity-90 font-bold">Kết luận định hướng:</div>
                        <div class="text-base sm:text-lg font-black font-heading">
                            @if($submission->need_doctor)
                                CẦN ĐƯA BÉ ĐI KHÁM BÁC SĨ CHUYÊN KHOA NHI TRONG VÒNG 1 - 2 TUẦN TỚI!
                            @elseif($submission->risk_level === 'medium')
                                NÊN ĐƯỢC CHUYÊN GIA ĐÁNH GIÁ TRỰC TIẾP HOẶC THEO DÕI SÁT TẠI NHÀ 4 TUẦN
                            @else
                                HIỆN TẠI CHƯA CẦN ĐI KHÁM BÁC SĨ - DUY TRÌ TƯƠNG TÁC TẠI NHÀ
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-slate-700 text-xs sm:text-sm leading-relaxed mb-6 font-medium">
                {{ $submission->doctor_recommendation }}
            </p>

            @if($submission->need_doctor)
                <!-- Actionable Checklist for Doctor Visit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <!-- Col 1: Chuyên khoa & Xét nghiệm cần làm -->
                    <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-sm">
                        <h4 class="font-bold text-sm text-rose-900 mb-2 flex items-center gap-1.5">
                            <span>🏥</span> Chuyên khoa & Đánh giá cần thực hiện:
                        </h4>
                        <ul class="text-xs text-slate-600 space-y-2">
                            <li class="flex items-start gap-1.5">
                                <span class="text-rose-500 font-bold">1.</span>
                                <span><strong>Khoa Tâm bệnh / Tâm thần Nhi:</strong> Thực hiện trắc nghiệm chẩn đoán lâm sàng chuẩn vàng (ADOS-2, Denver II, thang Mullen).</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-rose-500 font-bold">2.</span>
                                <span><strong>Đo Thính Lực (ABR / OAE):</strong> Bắt buộc đo điện thính giác thân não để loại trừ hoàn toàn khiếm thính tiềm ẩn.</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-rose-500 font-bold">3.</span>
                                <span><strong>Khám Tai Mũi Họng & Miệng:</strong> Kiểm tra dính thắng lưỡi (Ankyloglossia), viêm tai giữa ứ dịch, vòm họng.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 2: Phụ huynh cần chuẩn bị gì trước khi đi khám -->
                    <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-sm">
                        <h4 class="font-bold text-sm text-rose-900 mb-2 flex items-center gap-1.5">
                            <span>📱</span> Cha mẹ cần chuẩn bị trước khi đến viện:
                        </h4>
                        <ul class="text-xs text-slate-600 space-y-2">
                            <li class="flex items-start gap-1.5">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span><strong>Quay 2-3 video tại nhà (khoảng 3-5 phút):</strong> Quay lúc bé chơi tự do, lúc gọi tên bé, hoặc lúc bé có hành vi bất thường/ăn vạ (bé thường nhút nhát hoặc không bộc lộ khi đến phòng khám lạ).</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span><strong>Mang theo phiếu in kết quả này:</strong> Bấm nút "In Phiếu Gửi Bác Sĩ" ở đầu trang để bác sĩ nắm nhanh các cờ đỏ quan sát.</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span><strong>Ghi nhớ mốc phát triển:</strong> Tháng tuổi biết lẫy, ngồi, đi, từ đơn đầu tiên (nếu có).</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Hospital Link Button -->
                <div class="mt-6 flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ route('medical-centers.index') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm text-center transition flex items-center justify-center gap-2 shadow-md">
                        <span>Tra Cứu Bệnh Viện & Cơ Sở Y Tế Tuyến Đầu</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="{{ route('articles.compare') }}" class="w-full sm:w-auto px-5 py-3.5 rounded-2xl bg-white border border-slate-300 hover:border-rose-400 text-slate-700 hover:text-rose-700 font-bold text-xs sm:text-sm text-center transition">
                        Đọc kỹ: Phân biệt Chậm nói vs Phổ tự kỷ
                    </a>
                </div>
            @endif
        </div>

        <!-- DETECTED RED FLAGS BREAKDOWN (NẾU CÓ) -->
        @if(!empty($submission->detected_red_flags) && count($submission->detected_red_flags) > 0)
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 mb-8">
                <div class="flex items-center justify-between gap-3 mb-6">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 font-heading flex items-center gap-2">
                        <span>🚩</span> Chi tiết {{ count($submission->detected_red_flags) }} Dấu Hiệu Báo Động Đỏ Được Ghi Nhận
                    </h2>
                    <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-black">
                        {{ count($submission->detected_red_flags) }} cờ đỏ
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($submission->detected_red_flags as $rf)
                        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50/50 border border-rose-200/80">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h3 class="font-bold text-sm sm:text-base text-slate-900">
                                    {{ $rf['question'] }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-md bg-rose-200/80 text-rose-900 text-[10px] font-bold uppercase shrink-0">
                                    {{ $rf['category'] }}
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-rose-700 font-medium flex items-center gap-1.5">
                                <span>⚠️ Ý nghĩa cảnh báo:</span>
                                <span>{{ $rf['flag_reason'] }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- ACTION PLAN: BỐ MẸ NÊN LÀM GÌ TẠI NHÀ NGAY HÔM NAY -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 mb-8">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 font-heading mb-6 flex items-center gap-2">
                <span>🎯</span> 4 Hành Động Cần Thực Hiện Ngay Tại Nhà
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black text-xs mb-2">1</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Cắt 100% thiết bị điện tử trong 30 ngày</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tắt tivi, điện thoại thông minh, máy tính bảng và các kênh YouTube âm thanh dồn dập. Đây là bước đầu tiên để "giải phóng" não bộ của trẻ khỏi tình trạng quá tải hình ảnh thụ động.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-sky-50/60 border border-sky-100">
                    <div class="w-8 h-8 rounded-xl bg-sky-500 text-white flex items-center justify-center font-black text-xs mb-2">2</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Kỹ thuật 5 giây kiên nhẫn chờ đợi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Không vội đoán ý và làm thay bé. Khi bé muốn nước hoặc đồ chơi, đưa món đồ ngang tầm mắt bạn, nói mẫu từ khóa 1-2 lần rồi nhìn mắt bé đếm thầm 5 giây tạo áp lực giao tiếp để bé bật âm.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-xs mb-2">3</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Trò chơi tương tác mặt đối mặt 2 giờ/ngày</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Chơi thổi bong bóng xà phòng, ú òa, đu quay, cù léc, đóng giả tiếng con vật (gâu gâu, meo meo, còi bíp bíp). Các từ tượng thanh dễ bắt chước hơn rất nhiều so với từ ngữ phức tạp.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100">
                    <div class="w-8 h-8 rounded-xl bg-purple-500 text-white flex items-center justify-center font-black text-xs mb-2">4</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Đồng hành cùng Lộ trình 30 ngày</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Thực hiện mỗi ngày 1 bài tập kích hoạt ngôn ngữ và câu thần chú giao tiếp đã được các chuyên gia thiết kế sẵn.
                    </p>
                    <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-purple-700 hover:text-purple-900 mt-2">
                        <span>Bắt đầu Lộ trình 30 ngày</span> →
                    </a>
                </div>
            </div>
        </div>

        <!-- PRINT FOOTER (ONLY VISIBLE ON PRINT) -->
        <div class="hidden print:block text-xs text-slate-500 border-t border-slate-300 pt-4 mt-8">
            <p>Trang web: http://127.0.0.1:8000 | Cổng thông tin phi lợi nhuận hỗ trợ cha mẹ</p>
            <p class="italic mt-1">* Phiếu đánh giá này được in ra nhằm mục đích hỗ trợ cha mẹ cung cấp thông tin hành vi quan sát tại nhà cho Bác sĩ thăm khám lâm sàng.</p>
        </div>

    </div>
</div>
@endsection
