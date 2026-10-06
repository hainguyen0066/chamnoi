@extends('layouts.admin')

@section('title', 'Cấu Hình Treo Quảng Cáo & Kiếm Tiền (Monetization) - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
            <span>💰</span>
            <span>Cấu Hình Kiếm Tiền & Treo Quảng Cáo</span>
        </h2>
        <p class="text-xs text-slate-500 mt-1">
            Quản lý mã quảng cáo Google AdSense, banner tài trợ từ các trung tâm / sản phẩm mẹ & bé, và tiếp thị liên kết (Affiliate).
        </p>
    </div>

    <!-- Guide / Monetization Potential Notice -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-emerald-50 border border-amber-200/80 text-xs sm:text-sm text-slate-700 leading-relaxed shadow-sm">
        <strong class="text-amber-900 block font-bold text-sm mb-1">💡 Cơ hội kiếm tiền từ website Chậm Nói:</strong>
        <p>
            Chủ đề <em>"Chậm nói ở trẻ"</em> và <em>"Nuôi dạy con"</em> có chỉ số giá thầu quảng cáo (CPC) của Google AdSense rất cao tại Việt Nam (từ các hãng sữa, đồ chơi giáo dục, trung tâm can thiệp âm ngữ, bệnh viện tư). Khi website có lượng truy cập ổn định từ Google Search (SEO), bạn có thể bật Google AdSense tự động hoặc nhận đặt banner độc quyền với giá 2.000.000đ - 10.000.000đ/tháng từ các trung tâm phục hồi chức năng!
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.ads.update') }}" class="space-y-6">
        @csrf

        <!-- 1. Google AdSense -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xl">🌐</span>
                <div>
                    <h3 class="font-bold text-base text-slate-800">1. Google AdSense (Quảng cáo tự động của Google)</h3>
                    <p class="text-xs text-slate-400">Kiếm tiền khi người đọc click hoặc xem quảng cáo</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Mã Google AdSense Publisher ID (ca-pub-...)
                    </label>
                    <input type="text" name="adsense_client_id"
                           value="{{ old('adsense_client_id', $settings['adsense_client_id']->value ?? '') }}"
                           placeholder="ca-pub-1234567890123456"
                           class="w-full px-4 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Trạng thái Google Auto Ads
                    </label>
                    <select name="adsense_auto_ads_enabled" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                        <option value="0" {{ ($settings['adsense_auto_ads_enabled']->value ?? '0') === '0' ? 'selected' : '' }}>Tắt (Chưa kích hoạt)</option>
                        <option value="1" {{ ($settings['adsense_auto_ads_enabled']->value ?? '0') === '1' ? 'selected' : '' }}>Bật (Tự động chèn quảng cáo thông minh)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Quảng cáo chèn trong nội dung bài viết (In-Article Ads) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl">📰</span>
                    <div>
                        <h3 class="font-bold text-base text-slate-800">2. Quảng Cáo Trong Bài Viết (In-Article Banner)</h3>
                        <p class="text-xs text-slate-400">Tự động xuất hiện ở giữa bài viết sau đoạn văn thứ 2 (Vị trí click cao nhất)</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="ad_in_article_enabled" value="1" {{ ($settings['ad_in_article_enabled']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-slate-700">Kích hoạt</span>
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Mã HTML / Script Banner chèn trong bài viết
                </label>
                <textarea name="ad_in_article_code" rows="4" placeholder="Dán mã AdSense In-Article hoặc mã HTML banner tài trợ (ảnh + link shopee/khóa học/sữa)..."
                          class="w-full px-4 py-2 text-xs font-mono rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('ad_in_article_code', $settings['ad_in_article_code']->value ?? '') }}</textarea>
            </div>
        </div>

        <!-- 3. Hộp Tiếp Thị Liên Kết / Sản Phẩm Khuyên Dùng (Affiliate Box) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xl">🎁</span>
                    <div>
                        <h3 class="font-bold text-base text-slate-800">3. Hộp Sản Phẩm Tiếp Thị Liên Kết (Affiliate Khuyên Dùng)</h3>
                        <p class="text-xs text-slate-400">Hiển thị ở chân bài viết (Giới thiệu bộ thẻ flashcard, đồ chơi Montessori, sách vải)</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="ad_sidebar_affiliate_enabled" value="1" {{ ($settings['ad_sidebar_affiliate_enabled']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-slate-700">Kích hoạt</span>
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Nội dung HTML Banner giới thiệu sản phẩm / Link hoa hồng Shopee
                </label>
                <textarea name="ad_sidebar_affiliate_code" rows="4" placeholder="<div class='p-4 bg-emerald-50 rounded-2xl'><h3>Bộ Flashcard Kích Âm Cho Bé</h3>...</div>"
                          class="w-full px-4 py-2 text-xs font-mono rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('ad_sidebar_affiliate_code', $settings['ad_sidebar_affiliate_code']->value ?? '') }}</textarea>
            </div>
        </div>

        <!-- 4. Google Analytics 4 (Theo dõi chỉ số chuyên sâu) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xl">📈</span>
                <div>
                    <h3 class="font-bold text-base text-slate-800">4. Google Analytics 4 (GA4 Tracking ID)</h3>
                    <p class="text-xs text-slate-400">Tự động gắn mã theo dõi người dùng từ Google</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Mã Google Measurement ID (G-XXXXXXXXXX)
                </label>
                <input type="text" name="google_analytics_id"
                       value="{{ old('google_analytics_id', $settings['google_analytics_id']->value ?? '') }}"
                       placeholder="G-ABC123XYZ"
                       class="w-full px-4 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition">
                Lưu Cấu Hình Kiếm Tiền & Quảng Cáo
            </button>
        </div>
    </form>

</div>
@endsection
