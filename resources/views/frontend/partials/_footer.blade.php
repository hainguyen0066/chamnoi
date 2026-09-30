{{--
    Footer dùng chung cho mọi trang frontend — sửa 1 chỗ, đổi mọi nơi.
    Link social đọc từ Settings (admin > Cấu hình hệ thống).
--}}
@php
    $footerFanpage = \App\Models\Setting::get('social_fanpage') ?: '#';
    $footerYoutube = \App\Models\Setting::get('social_youtube') ?: '#';
    $footerZalo = \App\Models\Setting::get('social_zalo') ?: '#';
@endphp

<footer class="footer">
    <div class="footer-inner">
        <div class="product-info">
            <img class="product-icon" src="{{ asset('frontend/assets/images/avt.png') }}" alt="">
            <div class="product-table">
                <span class="info-label">Tên sản phẩm:</span>
                <span class="info-value">Tình Trong Thiên Hạ</span>

                <span class="info-label">Thiết bị tương thích:</span>
                <span class="info-value">Điện thoại thông minh, máy tính bảng và PC</span>

                <span class="info-label">Cấu hình:</span>
                <span class="info-value">Android 5.1, RAM 2GB trở lên iOS 9.0, iPhone 6 trở lên</span>

                <span class="info-label">Dung lượng yêu cầu:</span>
                <span class="info-value">2.5GB</span>
            </div>
        </div>

        <div class="social-row">
            <a class="social-link" href="{{ $footerFanpage }}" target="_blank" rel="noopener">FANPAGE</a>
            <span class="social-divider">|</span>
            <a class="social-link" href="{{ $footerZalo }}" target="_blank" rel="noopener">GROUP</a>
            <span class="social-divider">|</span>
            <a class="social-link" href="#">TIKTOK</a>
            <span class="social-divider">|</span>
            <a class="social-link" href="{{ $footerYoutube }}" target="_blank" rel="noopener">YOUTUBE</a>
        </div>

        <div class="partners-row">
            <img class="partner-logo" src="{{ asset('frontend/assets/images/logo.png') }}" alt="">
        </div>

        <div class="legal-block">
			<p>Giấy phép cung cấp dịch vụ trò chơi điện tử G1 trên mạng số: 117/GP-PTTH&TTĐT 
			do Bộ Thông tin và Truyền thông cấp ngày 17/06/2026</p>
			<p>Quyết định phát hành trò chơi số: 491/QĐ-PTTH&TTĐT bởi LOHIGAME CO,.LTD ngày 10/08/2026</p>
			<p>Trụ sở chính: 139/9 Năm Châu, Phường Bảy Hiền, TP. Hồ Chí Minh, Việt Nam
			</p>
		</div>
    </div>
</footer>
