@extends('layouts.app')

@section('title', 'Mầm Ngôn Ngữ - Cẩm Nang Toàn Diện Về Chậm Nói Ở Trẻ')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/80 via-white to-amber-50/30 pt-12 pb-20 lg:pt-16 lg:pb-28">
        <!-- Decorative blurred circles -->
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-gradient-to-tr from-emerald-200/40 to-sky-200/40 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Column: Value Prop & Actions -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100/80 text-emerald-800 text-xs sm:text-sm font-bold border border-emerald-200/60 shadow-sm">
                        <span>🌱</span>
                        <span>Đồng hành cùng hàng nghìn gia đình có con chậm nói</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15] font-heading">
                        Con chậm nói <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-sky-600 bg-clip-text text-transparent">Đừng vội hoang mang</span>, <br>
                        hãy thấu hiểu để can thiệp đúng!
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Cẩm nang khoa học giúp cha mẹ <strong class="text-slate-900 font-semibold">phân biệt rạch ròi chậm nói đơn thuần với tự kỷ</strong>, nắm bắt nguyên nhân cốt lõi và tự tin áp dụng bài tập can thiệp bật âm tại nhà mỗi ngày.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('screening.index') }}" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-sky-600 hover:from-emerald-700 hover:to-sky-700 text-white font-bold text-base shadow-xl shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:-translate-y-0.5 transition duration-300 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Làm bài trắc nghiệm sàng lọc ngay</span>
                        </a>

                        <a href="{{ route('articles.compare') }}" class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-white hover:bg-rose-50 text-rose-700 border-2 border-rose-200 hover:border-rose-400 font-bold text-base shadow-sm transition duration-300 flex items-center justify-center gap-2">
                            <span>🔍 Phân biệt Chậm nói & Tự kỷ</span>
                        </a>
                    </div>

                    <!-- Trust indicators -->
                    <div class="pt-6 grid grid-cols-3 gap-4 border-t border-slate-200/80 max-w-lg mx-auto lg:mx-0 text-center lg:text-left">
                        <div>
                            <div class="text-2xl font-black text-emerald-700 font-heading">0 - 3 Tuổi</div>
                            <div class="text-xs text-slate-500 font-medium">Giai đoạn cửa sổ vàng</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-sky-700 font-heading">90%</div>
                            <div class="text-xs text-slate-500 font-medium">Cải thiện nếu can thiệp sớm</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-amber-700 font-heading">Chuẩn CDC</div>
                            <div class="text-xs text-slate-500 font-medium">Thang mốc y khoa</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Quick Screener Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl shadow-slate-200/80 border border-emerald-100 relative">
                        <div class="absolute -top-3.5 right-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                            Miễn phí 100%
                        </div>

                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-lg">
                                📋
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-heading">Sàng lọc nhanh theo độ tuổi</h3>
                                <p class="text-xs text-slate-500">Chọn tháng tuổi của con để kiểm tra mốc phát triển</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('screening.index', ['age' => '12-18m']) }}" class="flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-bold text-xs flex items-center justify-center">1</div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-800 group-hover:text-emerald-700">Trẻ 12 – 18 tháng</div>
                                        <div class="text-xs text-slate-500">Chỉ tay, gọi tên quay lại, 1-3 từ đơn</div>
                                    </div>
                                </div>
                                <span class="text-emerald-600 font-bold text-xs group-hover:translate-x-1 transition">Khám phá &rarr;</span>
                            </a>

                            <a href="{{ route('screening.index', ['age' => '18-24m']) }}" class="flex items-center justify-between p-3.5 rounded-2xl border-2 border-emerald-300 bg-emerald-50/40 hover:bg-emerald-100/60 transition group shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white font-bold text-xs flex items-center justify-center">2</div>
                                    <div>
                                        <div class="font-bold text-sm text-emerald-900">Trẻ 18 – 24 tháng <span class="text-[10px] bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded font-bold">Phổ biến</span></div>
                                        <div class="text-xs text-emerald-700">Vốn từ 20+, ghép 2 từ, chú ý chung</div>
                                    </div>
                                </div>
                                <span class="text-emerald-800 font-bold text-xs group-hover:translate-x-1 transition">Làm ngay &rarr;</span>
                            </a>

                            <a href="{{ route('screening.index', ['age' => '2-3y']) }}" class="flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 font-bold text-xs flex items-center justify-center">3</div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-800 group-hover:text-emerald-700">Trẻ 2 – 3 tuổi</div>
                                        <div class="text-xs text-slate-500">Câu ngắn 3-4 từ, hỏi cái gì đây, chơi giả vờ</div>
                                    </div>
                                </div>
                                <span class="text-emerald-600 font-bold text-xs group-hover:translate-x-1 transition">Khám phá &rarr;</span>
                            </a>

                            <a href="{{ route('screening.index', ['age' => '3-5y']) }}" class="flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center">4</div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-800 group-hover:text-emerald-700">Trẻ 3 – 5 tuổi</div>
                                        <div class="text-xs text-slate-500">Kể chuyện ngắn, đại từ nhân xưng, nói lưu loát</div>
                                    </div>
                                </div>
                                <span class="text-emerald-600 font-bold text-xs group-hover:translate-x-1 transition">Khám phá &rarr;</span>
                            </a>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <a href="{{ route('behavior-assessment.index') }}" class="block p-3.5 rounded-2xl bg-gradient-to-r from-rose-500 via-amber-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white shadow-md shadow-rose-500/20 group transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xl">🚨</span>
                                        <div>
                                            <div class="font-black text-xs sm:text-sm">Bộ Phân Tích Hành Vi & Cờ Đỏ Y Tế</div>
                                            <div class="text-[11px] text-rose-100">Chấm điểm M-CHAT-R & Phát hiện cờ đỏ báo động</div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-white group-hover:translate-x-1 transition">&rarr;</span>
                                </div>
                            </a>
                        </div>

                        <div class="mt-3 p-3 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-2.5">
                            <span class="text-amber-600 text-base">🛡️</span>
                            <span><strong>Lưu ý:</strong> Mọi bài test trên website mang tính chất tham khảo & định hướng, không thay thế chẩn đoán bác sĩ.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VIRAL TOOLS SPOTLIGHT: Kéo User & Tăng Thời Gian On-Site -->
    <section class="py-8 bg-slate-100/70 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Card 1: Kiểm Tra Vốn Từ -->
                <a href="{{ route('vocabulary.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 hover:border-emerald-500 hover:shadow-lg transition flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-3xl shrink-0 group-hover:scale-110 transition">
                        🔤
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-sm text-slate-900 group-hover:text-emerald-700">Kiểm Tra Vốn Từ Của Bé</span>
                            <span class="px-1.5 py-0.5 rounded bg-rose-500 text-white text-[9px] font-extrabold uppercase">Mới</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Đếm số từ bé đã nói & đối chiếu chuẩn CDC</p>
                    </div>
                </a>

                <!-- Card 2: Vòng Quay Trò Chơi -->
                <a href="{{ route('vocabulary.index') }}#wheel" class="p-5 rounded-3xl bg-white border border-slate-200/80 hover:border-amber-500 hover:shadow-lg transition flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-3xl shrink-0 group-hover:scale-110 transition">
                        🎡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-sm text-slate-900 group-hover:text-amber-700">Vòng Quay Trò Chơi Mỗi Ngày</span>
                            <span class="px-1.5 py-0.5 rounded bg-amber-500 text-white text-[9px] font-extrabold uppercase">Hot</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Quay ngẫu nhiên 1 hoạt động 5 phút kích âm</p>
                    </div>
                </a>

                <!-- Card 3: Thẻ Kích Âm Có Audio -->
                <a href="{{ route('flashcards.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 hover:border-teal-500 hover:shadow-lg transition flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center text-3xl shrink-0 group-hover:scale-110 transition">
                        🎴
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-sm text-slate-900 group-hover:text-teal-700">Thẻ Kích Âm (Có Tiếng)</span>
                            <span class="px-1.5 py-0.5 rounded bg-teal-600 text-white text-[9px] font-extrabold uppercase">21 Âm</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Bấm nghe giọng mẫu tiếng con vật & còi xe</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    <!-- 3 Big Pillars Section (3 Trụ Cột Tri Thức Phụ Huynh Phải Biết) -->
    <section class="py-16 bg-white border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 font-heading">
                    3 Câu Hỏi Lớn Khiến Cha Mẹ Mất Ngủ
                </h2>
                <p class="text-slate-600 mt-3 text-sm sm:text-base">
                    Gạt bỏ những lời khuyên truyền miệng thiếu căn cứ, cùng giải mã khoa học một cách đơn giản và dễ hiểu nhất.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1: Nguyên Nhân -->
                <div class="rounded-3xl p-8 bg-gradient-to-b from-sky-50/80 to-white border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-600 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition">
                            📱
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 font-heading mb-3">1. Vì sao con chậm nói?</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Hơn 70% các ca chậm nói hiện nay do môi trường: lạm dụng thiết bị điện thoại/tivi dẫn tới giao tiếp 1 chiều, người lớn quá chiều chuộng hoặc ít trò chuyện. Chỉ một số ít do thắng lưỡi hay thính lực.
                        </p>
                    </div>
                    <a href="{{ route('articles.index', ['category' => 'nguyen-nhan']) }}" class="inline-flex items-center gap-1.5 font-bold text-sm text-sky-700 hover:text-sky-800">
                        Xem chi tiết nguyên nhân &rarr;
                    </a>
                </div>

                <!-- Pillar 2: Phân Biệt Tự Kỷ -->
                <div class="rounded-3xl p-8 bg-gradient-to-b from-rose-50/80 to-white border-2 border-rose-200 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-rose-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                        Rất quan trọng
                    </div>
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition">
                            🧩
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 font-heading mb-3">2. Con có bị tự kỷ không?</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Không phải trẻ chậm nói nào cũng tự kỷ! Tiêu chí quyết định là <strong>sự tương tác xã hội</strong>: giao tiếp mắt, chỉ tay rủ chơi, biểu cảm nét mặt và khả năng hiểu mệnh lệnh của cha mẹ.
                        </p>
                    </div>
                    <a href="{{ route('articles.compare') }}" class="inline-flex items-center gap-1.5 font-bold text-sm text-rose-700 hover:text-rose-800">
                        Xem bảng so sánh 6 tiêu chí &rarr;
                    </a>
                </div>

                <!-- Pillar 3: Cách Xử Lý & Can Thiệp -->
                <div class="rounded-3xl p-8 bg-gradient-to-b from-emerald-50/80 to-white border border-emerald-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition">
                            🧸
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 font-heading mb-3">3. Phải làm gì tại nhà?</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Áp dụng nguyên tắc vàng <strong>3T (Tắt thiết bị - Tắm ngôn ngữ - Tương tác mặt đối mặt)</strong>, kỹ thuật chờ đợi 5 giây và các trò chơi vận động cơ miệng đơn giản nhưng mang lại hiệu quả vượt trội.
                        </p>
                    </div>
                    <a href="{{ route('articles.index', ['category' => 'cach-xu-ly']) }}" class="inline-flex items-center gap-1.5 font-bold text-sm text-emerald-700 hover:text-emerald-800">
                        Học phương pháp 3T &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE FEATURE: Interactive Speech Delay vs Autism Differential Checker -->
    <section class="py-16 bg-gradient-to-b from-rose-50/50 via-white to-amber-50/40 border-b border-rose-100" id="phan-biet-tu-ky-section" x-data="{
        answers: {
            eye_contact: 'normal',
            pointing: 'normal',
            name_response: 'normal',
            repetitive: 'normal',
            gestures: 'normal',
            play: 'normal'
        },
        getAutismScore() {
            let score = 0;
            for (let k in this.answers) {
                if (this.answers[k] === 'autism') score++;
            }
            return score;
        },
        getAssessment() {
            const score = this.getAutismScore();
            if (score >= 3) {
                return {
                    level: 'high_autism',
                    badge: '🚩 CẢNH BÁO: CÓ NHIỀU DẤU HIỆU CỦA PHỔ TỰ KỶ (ASD)',
                    badgeClass: 'bg-rose-600 text-white',
                    title: 'Bé có từ 3 dấu hiệu rủi ro về tương tác xã hội & hành vi',
                    desc: 'Các biểu hiện tránh né ánh mắt, không chỉ tay khoe đồ, ít quay lại khi gọi tên là cờ đỏ lâm sàng quan trọng. Cha mẹ NÊN ĐƯA BÉ ĐI KHÁM CHUYÊN KHOA NHI SỚM (Khoa Tâm lý - Thần kinh) để đánh giá thang đo M-CHAT/Denver II, không nên tiếp tục chờ đợi thụ động tại nhà.',
                    ctaText: 'Xem danh sách Bệnh viện Nhi Đồng & Phòng khám uy tín',
                    ctaUrl: '{{ route('medical-centers.index') }}'
                };
            } else if (score >= 1) {
                return {
                    level: 'medium',
                    badge: '⚠️ CẦN THEO DÕI SÁT: CÓ 1 - 2 DẤU HIỆU CẦN LƯU Ý',
                    badgeClass: 'bg-amber-500 text-white',
                    title: 'Bé có một vài biểu hiện giao tiếp chưa đạt mốc chuẩn',
                    desc: 'Có thể do con tiếp xúc với màn hình tivi/điện thoại quá sớm dẫn đến giao tiếp 1 chiều, hoặc thiếu môi trường tương tác mặt đối mặt. Cha mẹ hãy áp dụng ngay nguyên tắc 3T và lộ trình 30 ngày tại nhà, đồng thời theo dõi tiến triển trong 4-6 tuần.',
                    ctaText: 'Bắt đầu Lộ trình 30 ngày cùng con bật âm',
                    ctaUrl: '{{ route('roadmap.index') }}'
                };
            } else {
                return {
                    level: 'simple_delay',
                    badge: '🌱 KHẢ NĂNG CAO: CHẬM NÓI ĐƠN THUẦN (EXPRESSIVE DELAY)',
                    badgeClass: 'bg-emerald-600 text-white',
                    title: 'Bé có nhu cầu kết nối xã hội tốt, chỉ bị chậm về phát âm',
                    desc: 'Con nhìn vào mắt mẹ, biết chỉ tay, kéo tay rủ chơi, biết cười đùa chia sẻ cảm xúc. Đây là tín hiệu rất tích cực! Con KHÔNG CÓ CỜ ĐỎ TỰ KỶ. Cha mẹ hoàn toàn có thể an tâm, kiên trì kích hoạt luồng hơi và tập từ đơn mỗi ngày.',
                    ctaText: 'Xem các trò chơi kích âm và thẻ phát âm Flashcard',
                    ctaUrl: '{{ route('flashcards.index') }}'
                };
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="px-3.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold text-xs uppercase tracking-wider border border-rose-200">
                    Trọng tâm lo âu của cha mẹ
                </span>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 font-heading mt-3 mb-3">
                    Công Cụ Tự Đối Chiếu: "Chậm Nói Hay Tự Kỷ?"
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Chọn nhanh các biểu hiện bé thể hiện hàng ngày để hệ thống đo lường khuynh hướng lâm sàng sơ bộ giúp cha mẹ giải tỏa áp lực tâm lý.
                </p>
            </div>

            <!-- 6 Interactive Criteria Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                <!-- Tiêu chí 1: Giao tiếp mắt -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-bold flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900 font-heading">Ánh mắt khi trò chuyện</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Khi bạn nói chuyện, gọi tên hoặc bế con trên tay:</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.eye_contact === 'normal' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.eye_contact" value="normal" class="sr-only">
                            <span>✓ Nhìn thẳng vào mắt mẹ, ánh mắt sống động, cười đáp lại</span>
                        </label>
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.eye_contact === 'autism' ? 'border-rose-500 bg-rose-50/70 text-rose-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.eye_contact" value="autism" class="sr-only">
                            <span>⚠ Tránh né ánh mắt, nhìn lướt qua như không thấy người đối diện</span>
                        </label>
                    </div>
                </div>

                <!-- Tiêu chí 2: Chỉ tay & Chú ý chung -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-bold flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900 font-heading">Cử chỉ chỉ tay (Pointing)</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Khi con muốn món đồ hoặc thấy con chó ngoài đường:</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.pointing === 'normal' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.pointing" value="normal" class="sr-only">
                            <span>✓ Dùng ngón tay trỏ chỉ đồ vật, nhìn mẹ rủ mẹ xem cùng</span>
                        </label>
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.pointing === 'autism' ? 'border-rose-500 bg-rose-50/70 text-rose-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.pointing" value="autism" class="sr-only">
                            <span>⚠ Cầm cổ tay mẹ đặt vào đồ vật như một công cụ cơ học</span>
                        </label>
                    </div>
                </div>

                <!-- Tiêu chí 3: Phản xạ khi gọi tên -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-bold flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900 font-heading">Phản ứng với tên gọi</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Khi bạn đứng sau lưng hoặc cách 1-2m gọi tên bé:</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.name_response === 'normal' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.name_response" value="normal" class="sr-only">
                            <span>✓ Quay đầu lại ngay khi nghe tiếng gọi</span>
                        </label>
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.name_response === 'autism' ? 'border-rose-500 bg-rose-50/70 text-rose-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.name_response" value="autism" class="sr-only">
                            <span>⚠ Gọi nhiều lần không quay lại (dù tiếng quảng cáo tivi vẫn nghe)</span>
                        </label>
                    </div>
                </div>

                <!-- Tiêu chí 4: Hành vi rập khuôn -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-bold flex items-center justify-center">4</span>
                            <h3 class="font-bold text-sm text-slate-900 font-heading">Hành vi lặp lại / Định hình</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Cách bé tương tác với đồ chơi và vận động cơ thể:</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.repetitive === 'normal' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.repetitive" value="normal" class="sr-only">
                            <span>✓ Chơi đồ chơi đa dạng, đẩy xe chạy, xếp khối, không rập khuôn</span>
                        </label>
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.repetitive === 'autism' ? 'border-rose-500 bg-rose-50/70 text-rose-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.repetitive" value="autism" class="sr-only">
                            <span>⚠ Xoay bánh xe, xếp hàng dài thẳng tắp, đi nhón gót, vẫy bàn tay</span>
                        </label>
                    </div>
                </div>

                <!-- Tiêu chí 5: Điệu bộ & Ngôn ngữ cơ thể -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-bold flex items-center justify-center">5</span>
                            <h3 class="font-bold text-sm text-slate-900 font-heading">Cử chỉ bù trừ lời nói</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Khi bé chưa nói được từ, bé thể hiện nhu cầu bằng cách:</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.gestures === 'normal' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.gestures" value="normal" class="sr-only">
                            <span>✓ Dùng điệu bộ phong phú: vẫy tay chào, lắc đầu, gật đầu, kéo tay</span>
                        </label>
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.gestures === 'autism' ? 'border-rose-500 bg-rose-50/70 text-rose-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.gestures" value="autism" class="sr-only">
                            <span>⚠ Rất ít điệu bộ, không lắc/gật đầu, khi không vừa ý thì ăn vạ gào thét</span>
                        </label>
                    </div>
                </div>

                <!-- Tiêu chí 6: Chơi giả vờ & Tương tác bạn bè -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white text-xs font-bold flex items-center justify-center">6</span>
                            <h3 class="font-bold text-sm text-slate-900 font-heading">Khả năng chơi giả vờ (Pretend Play)</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Khả năng tưởng tượng và bắt chước hành vi người lớn:</p>
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.play === 'normal' ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.play" value="normal" class="sr-only">
                            <span>✓ Biết cho gấu ăn thìa giả vờ, ru búp bê ngủ, cầm điện thoại a lô</span>
                        </label>
                        <label class="block p-3 rounded-2xl border cursor-pointer transition"
                               :class="answers.play === 'autism' ? 'border-rose-500 bg-rose-50/70 text-rose-900 font-bold' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" x-model="answers.play" value="autism" class="sr-only">
                            <span>⚠ Hoàn toàn không biết chơi giả vờ, chỉ thích một mình một góc</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Dynamic Result Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-200 relative overflow-hidden">
                <div class="max-w-3xl mx-auto text-center space-y-4">
                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-sm"
                          :class="getAssessment().badgeClass"
                          x-text="getAssessment().badge"></span>

                    <h3 class="text-xl sm:text-3xl font-black text-slate-900 font-heading"
                        x-text="getAssessment().title"></h3>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed bg-slate-50 p-4 sm:p-6 rounded-2xl border border-slate-100 text-left"
                       x-text="getAssessment().desc"></p>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a :href="getAssessment().ctaUrl"
                           class="w-full sm:w-auto px-7 py-3.5 rounded-2xl font-bold text-sm text-white shadow-lg transition"
                           :class="getAssessment().level === 'high_autism' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                           x-text="getAssessment().ctaText"></a>

                        <a href="{{ route('articles.compare') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl border border-slate-300 hover:bg-slate-100 font-bold text-xs sm:text-sm text-slate-700">
                            Xem bài viết phân tích chi tiết &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formula 3T Highlight Section -->
    <section class="py-16 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-xs uppercase tracking-wider border border-emerald-500/30">
                    Bí quyết can thiệp tại nhà
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white font-heading mt-3">
                    Quy Tắc Vàng 3T Trong Giao Tiếp Cùng Con
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-2">
                    Áp dụng kiên trì ít nhất 30 phút mỗi ngày liên tục trong 1 tháng sẽ thấy con tiến bộ bất ngờ
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/15 hover:border-emerald-400/50 transition">
                    <div class="text-3xl font-black text-rose-400 font-heading mb-2">01. TẮT</div>
                    <h3 class="text-lg font-bold text-white mb-2">Tắt Hoàn Toàn Thiết Bị Số</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Tắt tivi chạy nền, không cho con cầm điện thoại/iPad khi ăn cơm. Não bộ cần không gian tĩnh để lắng nghe giọng nói thật của con người.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/15 hover:border-emerald-400/50 transition">
                    <div class="text-3xl font-black text-sky-400 font-heading mb-2">02. TẮM</div>
                    <h3 class="text-lg font-bold text-white mb-2">Tắm Ngôn Ngữ Mọi Lúc</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Đóng vai "phát thanh viên" mô tả mọi thứ xung quanh con: "Mẹ đang cắt quả cam màu cam", "Bé đang xỏ giày đi chơi". Nói chậm, từ ngữ sinh động.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/15 hover:border-emerald-400/50 transition">
                    <div class="text-3xl font-black text-emerald-400 font-heading mb-2">03. TƯƠNG TÁC</div>
                    <h3 class="text-lg font-bold text-white mb-2">Giao Tiếp Mặt Đối Mặt</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Ngồi xổm xuống ngang tầm mắt con. Dừng lại 5 - 7 giây chờ đợi con mở miệng thay vì vội vàng đoán ý và đáp ứng ngay lập tức.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Tools Suite Section -->
    <section class="py-16 bg-white border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                    Hệ sinh thái đồng hành
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 font-heading mt-2">
                    Các Công Cụ Tương Tác Hỗ Trợ Phụ Huynh
                </h2>
                <p class="text-slate-600 text-sm sm:text-base mt-2">
                    Không chỉ là lý thuyết, chúng tôi cung cấp các công cụ trực quan giúp cha mẹ thực hành hàng ngày.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tool 1: 30 Days Roadmap -->
                <a href="{{ route('roadmap.index') }}" class="group bg-gradient-to-b from-emerald-50 to-white rounded-3xl p-6 border border-emerald-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-3xl mb-4 shadow-md shadow-emerald-500/20 group-hover:scale-110 transition">
                            📅
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Giáo trình 4 tuần</span>
                        <h3 class="text-lg font-bold text-slate-900 font-heading mt-2 mb-2 group-hover:text-emerald-700 transition">
                            Lộ Trình 30 Ngày "Cùng Con Bật Âm"
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            30 bài tập tương tác tại nhà có checklist theo dõi tiến độ và hướng dẫn từng bước.
                        </p>
                    </div>
                    <div class="pt-4 text-xs font-bold text-emerald-700 flex items-center gap-1 group-hover:translate-x-1 transition">
                        <span>Bắt đầu ngay</span> &rarr;
                    </div>
                </a>

                <!-- Tool 2: Milestones Calculator -->
                <a href="{{ route('milestones.index') }}" class="group bg-gradient-to-b from-sky-50 to-white rounded-3xl p-6 border border-sky-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-sky-500 text-white flex items-center justify-center text-3xl mb-4 shadow-md shadow-sky-500/20 group-hover:scale-110 transition">
                            🧮
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">Chuẩn CDC</span>
                        <h3 class="text-lg font-bold text-slate-900 font-heading mt-2 mb-2 group-hover:text-sky-700 transition">
                            Tính Tuổi & Mốc Chuẩn Phát Triển
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Nhập ngày sinh (và số tuần sinh non) để tra cứu chuẩn xác mốc ngôn ngữ của bé.
                        </p>
                    </div>
                    <div class="pt-4 text-xs font-bold text-sky-700 flex items-center gap-1 group-hover:translate-x-1 transition">
                        <span>Tính tuổi ngay</span> &rarr;
                    </div>
                </a>

                <!-- Tool 3: Sound Flashcards -->
                <a href="{{ route('flashcards.index') }}" class="group bg-gradient-to-b from-amber-50 to-white rounded-3xl p-6 border border-amber-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-3xl mb-4 shadow-md shadow-amber-500/20 group-hover:scale-110 transition">
                            🗣️
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Có giọng đọc mẫu</span>
                        <h3 class="text-lg font-bold text-slate-900 font-heading mt-2 mb-2 group-hover:text-amber-700 transition">
                            Thẻ Kích Âm & Luyện Cơ Miệng
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Nghe phát âm mẫu các âm thanh con vật, phương tiện giao thông và khẩu hình miệng.
                        </p>
                    </div>
                    <div class="pt-4 text-xs font-bold text-amber-700 flex items-center gap-1 group-hover:translate-x-1 transition">
                        <span>Luyện phát âm</span> &rarr;
                    </div>
                </a>

                <!-- Tool 4: Medical Centers Directory -->
                <a href="{{ route('medical-centers.index') }}" class="group bg-gradient-to-b from-rose-50 to-white rounded-3xl p-6 border border-rose-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center text-3xl mb-4 shadow-md shadow-rose-500/20 group-hover:scale-110 transition">
                            🏥
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">Bệnh viện đầu ngành</span>
                        <h3 class="text-lg font-bold text-slate-900 font-heading mt-2 mb-2 group-hover:text-rose-700 transition">
                            Danh Bạ Cơ Sở Y Tế Uy Tín
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Tra cứu địa chỉ, hotline và kinh nghiệm đặt lịch khám tại BV Nhi Đồng, Bạch Mai, Nhi TW...
                        </p>
                    </div>
                    <div class="pt-4 text-xs font-bold text-rose-700 flex items-center gap-1 group-hover:translate-x-1 transition">
                        <span>Tìm bệnh viện</span> &rarr;
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Articles Section -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-emerald-700 font-bold text-xs uppercase tracking-wider">Cẩm nang chuyên sâu</span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 font-heading mt-1">
                        Bài Viết & Hướng Dẫn Nổi Bật
                    </h2>
                </div>
                <a href="{{ route('articles.index') }}" class="font-bold text-sm text-emerald-700 hover:text-emerald-800 flex items-center gap-1.5 self-start sm:self-auto">
                    <span>Xem tất cả bài viết</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredArticles as $article)
                    <article class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200/80 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider
                                    @if($article->badge_color === 'rose') bg-rose-100 text-rose-800
                                    @elseif($article->badge_color === 'purple') bg-purple-100 text-purple-800
                                    @elseif($article->badge_color === 'amber') bg-amber-100 text-amber-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    {{ $article->badge_text ?? $article->category_label }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">{{ $article->reading_time }}</span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition font-heading mb-3 line-clamp-2">
                                <a href="{{ route('articles.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <p class="text-slate-600 text-sm leading-relaxed line-clamp-3 mb-6">
                                {{ $article->excerpt }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-medium text-slate-600">{{ $article->category_label }}</span>
                            <a href="{{ route('articles.show', $article->slug) }}" class="font-bold text-emerald-600 group-hover:translate-x-1 transition flex items-center gap-1">
                                Đọc tiếp &rarr;
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Comparison Preview Banner -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-r from-rose-500 via-pink-500 to-rose-600 text-white p-8 sm:p-12 shadow-2xl shadow-rose-500/20 relative overflow-hidden">
                <div class="relative z-10 max-w-2xl">
                    <span class="px-3 py-1 rounded-full bg-white/20 text-white font-bold text-xs uppercase tracking-wider inline-block mb-4">
                        Tâm điểm lo âu của cha mẹ
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black font-heading mb-4 leading-tight">
                        Bạn đang lo lắng: "Liệu con tôi có mắc phổ tự kỷ?"
                    </h2>
                    <p class="text-rose-100 text-sm sm:text-base leading-relaxed mb-8">
                        Đừng để nỗi bất an làm ảnh hưởng đến tinh thần của cả gia đình. Xem ngay bảng đối chiếu 6 dấu hiệu lâm sàng then chốt giữa Chậm nói đơn thuần và Rối loạn phổ tự kỷ (ASD).
                    </p>
                    <a href="{{ route('articles.compare') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-white text-rose-600 font-bold text-sm shadow-lg hover:bg-rose-50 hover:scale-105 transition duration-300">
                        <span>Mở Bảng So Sánh Chi Tiết</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
