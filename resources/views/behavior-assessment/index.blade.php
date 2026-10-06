@extends('layouts.app')

@section('title', $formData['meta']['title'] . ' - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-gradient-to-b from-emerald-50/40 via-slate-50 to-slate-100 py-10 lg:py-16" x-data="{ answeredCount: 0 }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Medical Disclaimer Banner (MANDATORY & PROMINENT) -->
        <div class="mb-8 p-4 sm:p-5 rounded-3xl bg-amber-500/10 border-2 border-amber-300 text-amber-950 shadow-sm flex items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-md">
                🛡️
            </div>
            <div class="text-xs sm:text-sm leading-relaxed">
                <strong class="font-bold text-amber-900 block text-sm sm:text-base mb-0.5">
                    Khuyến Cáo Y Tế Quan Trọng & Miễn Trừ Trách Nhiệm:
                </strong>
                <span>
                    Các bài trắc nghiệm và thang đo hành vi trên website (như M-CHAT-R, CDC, Sàng lọc Cờ đỏ) chỉ mang tính chất <strong>THAM KHẢO VÀ ĐỊNH HƯỚNG BAN ĐẦU</strong> giúp cha mẹ có căn cứ quan sát con có hệ thống. Kết quả <strong>TUYỆT ĐỐI KHÔNG THAY THẾ CHẨN ĐOÁN LÂM SÀNG</strong> của Bác sĩ Nhi khoa hoặc Bác sĩ Tâm thần nhi. Hãy luôn đưa bé đến bệnh viện chuyên khoa để được thăm khám trực tiếp.
                </span>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>{{ $formData['meta']['badge'] }}</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading tracking-tight mb-4">
                {{ $formData['meta']['title'] }}
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                {{ $formData['meta']['desc'] }}
            </p>
        </div>

        <!-- Form Selector Tabs (Chuyển đổi giữa các bộ form) -->
        <div class="bg-white rounded-3xl p-2.5 sm:p-3 shadow-md border border-slate-200/80 mb-8 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <a href="{{ route('behavior-assessment.index', ['form' => 'behavior_red_flags']) }}"
               class="flex flex-col items-center justify-center text-center p-3.5 rounded-2xl font-bold transition duration-200 {{ $currentForm === 'behavior_red_flags' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                <span class="text-base sm:text-lg mb-0.5">🚩 Cờ Đỏ & Hành Vi</span>
                <span class="text-[11px] font-normal {{ $currentForm === 'behavior_red_flags' ? 'text-emerald-100' : 'text-slate-400' }}">12 tháng - 5 tuổi (16 câu)</span>
            </a>

            <a href="{{ route('behavior-assessment.index', ['form' => 'mchat_r']) }}"
               class="flex flex-col items-center justify-center text-center p-3.5 rounded-2xl font-bold transition duration-200 {{ $currentForm === 'mchat_r' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                <span class="text-base sm:text-lg mb-0.5">🔬 Thang M-CHAT-R™</span>
                <span class="text-[11px] font-normal {{ $currentForm === 'mchat_r' ? 'text-emerald-100' : 'text-slate-400' }}">Sàng lọc Tự kỷ 16-30m (20 câu)</span>
            </a>

            <a href="{{ route('behavior-assessment.index', ['form' => 'receptive_hearing']) }}"
               class="flex flex-col items-center justify-center text-center p-3.5 rounded-2xl font-bold transition duration-200 {{ $currentForm === 'receptive_hearing' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                <span class="text-base sm:text-lg mb-0.5">👂 Hiểu Lệnh & Thính Lực</span>
                <span class="text-[11px] font-normal {{ $currentForm === 'receptive_hearing' ? 'text-emerald-100' : 'text-slate-400' }}">12 - 36 tháng (8 câu)</span>
            </a>
        </div>

        <!-- Quiz Form -->
        <form method="POST" action="{{ route('behavior-assessment.submit') }}" class="space-y-6" id="assessmentForm">
            @csrf
            <input type="hidden" name="form_type" value="{{ $currentForm }}">

            <!-- Guide Alert -->
            <div class="p-4 sm:p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-start gap-3">
                <span class="text-2xl">📋</span>
                <div class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    <strong class="font-bold text-slate-900 block mb-0.5">Cách trả lời chuẩn xác nhất:</strong>
                    <span>Hãy đánh giá dựa trên hành vi mà con bạn thể hiện thường ngày trong 1-2 tháng gần đây khi ở nhà với người thân. Nếu hành vi đó chỉ xảy ra 1-2 lần hiếm hoi hoặc lúc bé bị ốm/mệt thì không tính.</span>
                </div>
            </div>

            <!-- List of Questions -->
            <div class="space-y-6">
                @php $index = 1; @endphp
                @foreach($formData['questions'] as $qId => $q)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border {{ !empty($q['is_red_flag']) ? 'border-amber-200/80 hover:border-amber-400' : 'border-slate-200 hover:border-emerald-300' }} transition-all">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">
                                    {{ $index++ }}
                                </span>
                                @if(!empty($q['category']))
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[11px] font-bold">
                                        {{ $q['category'] }}
                                    </span>
                                @endif
                            </div>

                            @if(!empty($q['is_red_flag']))
                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-black uppercase tracking-wider shrink-0 flex items-center gap-1">
                                    <span>🚩</span> Dấu hiệu cờ đỏ
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading mb-2 leading-snug">
                            {{ $q['title'] }}
                        </h3>

                        @if(!empty($q['desc']))
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <strong class="text-slate-700">💡 Hướng dẫn quan sát:</strong> {{ $q['desc'] }}
                            </p>
                        @endif

                        <!-- Radio Options -->
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 pt-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="answers[{{ $qId }}]" value="yes" required class="peer sr-only">
                                <div class="p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/70 transition flex items-center justify-center gap-2 text-center text-xs sm:text-sm font-bold text-slate-700 peer-checked:text-emerald-800 hover:bg-slate-50">
                                    <span class="text-emerald-600 font-black">✓</span>
                                    <span>CÓ / Đúng với bé</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="answers[{{ $qId }}]" value="no" required class="peer sr-only">
                                <div class="p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 peer-checked:border-rose-400 peer-checked:bg-rose-50/70 transition flex items-center justify-center gap-2 text-center text-xs sm:text-sm font-bold text-slate-700 peer-checked:text-rose-800 hover:bg-slate-50">
                                    <span class="text-rose-500 font-black">✗</span>
                                    <span>KHÔNG / Bé chưa làm được</span>
                                </div>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Child and Parent Information Box -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 font-heading mb-1 flex items-center gap-2">
                    <span>👶</span> Thông tin của bé để phân tích chuẩn y khoa
                </h3>
                <p class="text-xs text-slate-500 mb-6">Tháng tuổi của bé là chỉ số quan trọng để hệ thống đối chiếu thang mốc phát triển tâm lý.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Tên ở nhà hoặc họ tên của bé</label>
                        <input type="text" name="child_name" placeholder="Ví dụ: Bé Bơ, Nguyễn Gia Huy"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">
                            Số tháng tuổi hiện tại của con <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="child_age_months" min="6" max="84" required placeholder="Ví dụ: 20 (tháng tuổi)"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-emerald-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Họ tên phụ huynh</label>
                        <input type="text" name="parent_name" placeholder="Ví dụ: Mẹ Mai Lan"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Số điện thoại phụ huynh (Không bắt buộc)</label>
                        <input type="tel" name="parent_phone" placeholder="0912 xxx xxx"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Submit Action Bar -->
            <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold text-base font-heading">Hoàn tất quan sát hành vi của con?</h4>
                    <p class="text-xs text-slate-400">Hệ thống sẽ tự động chấm điểm, phân tích cờ đỏ báo động và đưa ra kết luận đi khám bác sĩ.</p>
                </div>
                <button type="submit"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-500 hover:from-emerald-600 hover:to-sky-600 text-white font-black text-base shadow-lg shadow-emerald-500/30 hover:scale-[1.02] transition duration-300 flex items-center justify-center gap-2 shrink-0">
                    <span>Xem Kết Quả & Phân Tích Y Khoa</span>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
