@extends('layouts.app')

@section('title', 'Bảng So Sánh Chi Tiết: Chậm Nói Đơn Thuần vs Tự Kỷ (ASD) - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Phân biệt Chậm nói & Tự kỷ</span>
        </nav>

        <!-- Header -->
        <div class="max-w-3xl mb-12">
            <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold uppercase tracking-wider">
                Đối chiếu lâm sàng
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4 leading-tight">
                Phân Biệt Rạch Ròi: <br class="hidden sm:inline">
                <span class="text-emerald-600">Chậm Nói Đơn Thuần</span> vs <span class="text-rose-600">Phổ Tự Kỷ (ASD)</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                Hơn 80% phụ huynh tìm đến bác sĩ với tâm lý hoang mang tột độ rằng con mình bị tự kỷ khi thấy bé 2 tuổi chưa nói. Tuy nhiên, sự khác biệt mấu chốt nằm ở <strong>NHU CẦU KẾT NỐI VÀ GIAO TIẾP XÃ HỘI</strong> chứ không chỉ đơn thuần là việc bé nói được bao nhiêu từ.
            </p>
        </div>

        <!-- Interactive Comparison Matrix -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-200">
                <!-- Chậm nói đơn thuần -->
                <div class="p-6 sm:p-8 bg-emerald-50/30">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-bold">
                            🌱
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-emerald-900 font-heading">Trẻ Chậm Nói Đơn Thuần</h2>
                            <p class="text-xs text-emerald-700">Có nhu cầu giao tiếp nhưng thiếu vốn từ / công cụ phát âm</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-emerald-500">✓</span> Giao tiếp mắt (Eye contact)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Nhìn thẳng vào mắt cha mẹ, ánh mắt sống động, biết biểu cảm vui mừng khi được khen, xị mặt khi bị mắng.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-emerald-500">✓</span> Cử chỉ chỉ tay (Pointing)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Biết dùng ngón trỏ chỉ vào món đồ muốn xin; chỉ con chó, xe buýt rồi nhìn sang mẹ để rủ mẹ xem cùng (Chú ý chung).
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-emerald-500">✓</span> Phản ứng với tên gọi
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Quay đầu lại ngay khi ba mẹ gọi tên (trừ khi đang mải mê tập trung chơi món đồ bé thích).
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-emerald-500">✓</span> Ngôn ngữ cơ thể & Điệu bộ
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Dùng cử chỉ để bù đắp cho lời nói: kéo tay mẹ, gật đầu, lắc đầu, vẫy tay chào tạm biệt, khoanh tay xin xỏ.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-emerald-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-emerald-500">✓</span> Trò chơi & Tương tác
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Biết chơi giả vờ (cho thú bông ăn, lái xe giả vờ), thích chơi trốn tìm, ú oà và hào hứng khi có bạn bè đến nhà.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dấu hiệu Phổ Tự Kỷ -->
                <div class="p-6 sm:p-8 bg-rose-50/40">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl font-bold">
                            🧩
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-rose-900 font-heading">Trẻ Có Nguy Cơ Phổ Tự Kỷ (ASD)</h2>
                            <p class="text-xs text-rose-700">Khó khăn về giao tiếp tương tác xã hội và hành vi rập khuôn</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-white border border-rose-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-rose-500">⚠</span> Giao tiếp mắt (Eye contact)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Tránh né ánh mắt, nhìn lướt qua như không thấy người đối diện, ít khi nhìn vào mắt khi đòi hỏi hoặc khi được gọi.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-rose-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-rose-500">⚠</span> Cử chỉ chỉ tay (Pointing)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Không biết dùng ngón tay trỏ; thường cầm cả cổ tay người lớn kéo đặt vào đồ vật như một "công cụ cơ học".
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-rose-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-rose-500">⚠</span> Phản ứng với tên gọi
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Gọi nhiều lần không quay lại (dù thính lực hoàn toàn bình thường, tiếng mở bọc kẹo hay tiếng quảng cáo tivi vẫn nghe thấy).
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-rose-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-rose-500">⚠</span> Hành vi rập khuôn & Định hình
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Thích xoay bánh xe ô tô, nhìn chăm chú quạt trần, xếp đồ chơi thành hàng thẳng tắp, đi nhón gót, vẫy bàn tay trước mắt.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-rose-100 shadow-sm">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 flex items-center gap-2">
                                <span class="text-rose-500">⚠</span> Nói nhại lời vô nghĩa (Echolalia)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Nhắc lại y nguyên câu hỏi của người lớn ("Con ăn cơm chưa?" -> Bé lặp lại: "Con ăn cơm chưa?") hoặc đọc làu làu bảng chữ cái tiếng Anh mà không hiểu ngữ cảnh.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary callout -->
            <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-emerald-400 font-heading text-lg">Bạn vẫn còn băn khoăn về tình trạng của bé?</h3>
                    <p class="text-slate-300 text-xs sm:text-sm mt-0.5">Làm ngay bài sàng lọc 6 câu hỏi theo tháng tuổi để nhận đánh giá rủi ro sơ bộ chuẩn xác.</p>
                </div>
                <a href="{{ route('screening.index') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 font-bold text-sm text-white shrink-0 shadow-lg">
                    Làm bài trắc nghiệm ngay
                </a>
            </div>
        </div>

        @if(isset($compareArticle))
            <!-- Full deep-dive article content if available -->
            <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200 prose max-w-none mb-12">
                {!! $compareArticle->content !!}
            </div>
        @endif

        <!-- Diagnostic Protocol & Medical Steps -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200 space-y-8">
            <div class="border-b border-slate-100 pb-6">
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-sky-100 text-sky-800 uppercase tracking-wider">
                    Quy trình y khoa chính thức
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-2">
                    Bác Sĩ Chuyên Khoa Sẽ Khám & Đánh Giá Như Thế Nào?
                </h2>
                <p class="text-sm text-slate-600 mt-1">
                    Hiểu rõ quy trình giúp cha mẹ chuẩn bị tâm lý và cung cấp thông tin chính xác nhất cho bác sĩ.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-10 h-10 rounded-xl bg-sky-500 text-white font-bold flex items-center justify-center mb-3">1</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-2">Đo Thính Lực (Loại trừ khiếm thính)</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Bác sĩ sẽ nội soi tai mũi họng và đo nhĩ lượng hoặc ABR để đảm bảo bé không bị viêm tai giữa ứ dịch làm giảm thính lực (khiến bé nghe không rõ lời người lớn).
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-10 h-10 rounded-xl bg-rose-500 text-white font-bold flex items-center justify-center mb-3">2</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-2">Thang Sàng Lọc M-CHAT-R/F</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Thang đo 20 câu hỏi chuẩn quốc tế cho trẻ 16-30 tháng để đánh giá nguy cơ rối loạn phổ tự kỷ thông qua cử chỉ chỉ tay, giao tiếp mắt và chú ý chung.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white font-bold flex items-center justify-center mb-3">3</div>
                    <h3 class="font-bold text-sm text-slate-900 mb-2">Thang Phát Triển Denver II / Vineland</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Chuyên viên tâm lý sẽ trực tiếp chơi với bé để chấm điểm 4 lĩnh vực: Ngôn ngữ, Vận động tinh, Vận động thô và Cá nhân - Xã hội.
                    </p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-emerald-950 font-heading text-base">Xem Danh Bạ Các Bệnh Viện & Phòng Khám Nhi Tuyến Đầu</h3>
                    <p class="text-xs text-emerald-800 mt-0.5">Địa chỉ, hotline đặt hẹn tại BV Nhi Đồng 1, Nhi Đồng 2, Nhi TW, Bạch Mai...</p>
                </div>
                <a href="{{ route('medical-centers.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shrink-0">
                    Xem danh bạ bệnh viện
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
