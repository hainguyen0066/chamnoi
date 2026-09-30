{{--
    Popup Đăng Nhập / Đăng Ký (port từ .pop-login/.pop-register của template).
    Mở popup bằng class .btn-register / .btn-login (handler có sẵn trong main.js).
    Luồng đăng ký: nhập SĐT -> Gửi OTP (Zalo ZNS) -> nhập OTP -> Xác thực
    -> mở khoá nút ĐĂNG KÝ. Server chặn đăng ký nếu phone chưa verified.
    JS xử lý AJAX: public/frontend/assets/js/register-popup.js
--}}
@php $fe = fn (string $path) => asset('frontend/assets/' . $path); @endphp

{{-- ============ POPUP ĐĂNG KÝ ============ --}}
<div class="pop-register custom-popup">
    <div class="pop-close cpointer">
        <div class="img_hv">
            <img src="{{ $fe('images/close.png') }}">
            <img src="{{ $fe('images/close-active.png') }}">
        </div>
    </div>
    <ul class="register-menu">
        <li class="cta-dangnhap btn-login"><a href="javascript:void(0);">Đăng Nhập</a></li>
        <li class="cta-dangky btn-register"><a href="javascript:void(0);">Đăng Ký</a></li>
    </ul>
    <div class="register-box">
        <div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-user.png') }}" alt="" class="popup-icon">
                <input type="text" id="reg-fullname" placeholder="Họ tên người chơi" maxlength="100" autocomplete="off">
            </div>
            <div style="text-align: left;">
                @php
                    $maxYear = (int) date('Y') - 18;
                    $minYear = (int) date('Y') - 100;
                @endphp
                <div class="register--layout" style="gap:.3vw;">
                    <select id="reg-birthday-day" style="flex:1; padding-left:.5vw;">
                        <option value="">Ngày</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endfor
                    </select>
                    <select id="reg-birthday-month" style="flex:1; padding-left:.5vw;">
                        <option value="">Tháng</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">Tháng {{ $m }}</option>
                        @endfor
                    </select>
                    <select id="reg-birthday-year" style="flex:1.4; padding-left:.5vw;">
                        <option value="">Năm</option>
                        @for ($y = $maxYear; $y >= $minYear; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <span>*Người chơi chỉ có thể đăng ký tài khoản khi đủ từ 18 tuổi trở lên</span>
            </div>
            <div style="text-align: left;">
                <div class="register--layout" style="display:flex; align-items:center; gap:.5vw;">
                    <img src="{{ $fe('images/icon-phone.png') }}" alt="" class="popup-icon">
                    <input type="text" id="reg-phone" placeholder="Số điện thoại" maxlength="10" autocomplete="off" style="flex:1;">
                    <button type="button" id="btn-send-otp"
                            style="white-space:nowrap; padding:.45vw .8vw; background:#a03016; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:.8vw; font-weight:600;">
                        Gửi OTP
                    </button>
                </div>
                <span>*Số điện thoại cần xác thực qua mã OTP 6 chữ số gửi về Zalo</span>
            </div>
            <div id="otp-group" style="display:none; text-align:left;">
                <div class="register--layout" style="display:flex; align-items:center; gap:.5vw;">
                    <img src="{{ $fe('images/icon-pw.png') }}" alt="" class="popup-icon">
                    <input type="text" id="reg-otp" placeholder="Nhập mã OTP 6 số" maxlength="6" autocomplete="one-time-code" style="flex:1;">
                    <button type="button" id="btn-verify-otp"
                            style="white-space:nowrap; padding:.45vw .8vw; background:#1a7a3a; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:.8vw; font-weight:600;">
                        Xác thực
                    </button>
                </div>
                <span id="otp-verified-badge" style="display:none; color:#1a7a3a; font-weight:700;">✓ Số điện thoại đã xác thực</span>
            </div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-mail.png') }}" alt="" class="popup-icon icon-mail">
                <input type="email" id="reg-email" placeholder="Email" maxlength="255" autocomplete="off">
            </div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-user.png') }}" alt="" class="popup-icon">
                <input type="text" id="reg-username" placeholder="Tên đăng nhập (5-20 ký tự)" maxlength="20" autocomplete="new-username">
            </div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-pw.png') }}" alt="" class="popup-icon">
                <input type="password" id="reg-password" placeholder="Mật khẩu (tối thiểu 6 ký tự)" autocomplete="new-password">
            </div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-user.png') }}" alt="" class="popup-icon">
                <select id="reg-gender">
                    <option value="">Giới tính</option>
                    <option value="1">Nam</option>
                    <option value="2">Nữ</option>
                </select>
            </div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-map.png') }}" alt="" class="popup-icon">
                <input type="text" id="reg-address" placeholder="Địa chỉ liên lạc (nếu có)" maxlength="255" autocomplete="off">
            </div>
            <p id="register-message" style="display:none; margin:.5vw 0 0; font-size:.85vw; font-weight:600;"></p>
            <div class="submit">
                <a href="javascript:void(0);" id="btn-register-submit" class="cusor bright-12">ĐĂNG KÝ</a>
            </div>
        </div>
    </div>
</div>

{{-- ============ POPUP ĐĂNG NHẬP ============ --}}
<div class="pop-login custom-popup">
    <div class="pop-close cpointer">
        <div class="img_hv">
            <img src="{{ $fe('images/close.png') }}">
            <img src="{{ $fe('images/close-active.png') }}">
        </div>
    </div>
    <ul class="register-menu">
        <li class="cta-dangnhap btn-login"><a href="javascript:void(0);">Đăng Nhập</a></li>
        <li class="cta-dangky btn-register"><a href="javascript:void(0);">Đăng Ký</a></li>
    </ul>
    <div class="register-box">
        <div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-user.png') }}" alt="" class="popup-icon">
                <input type="text" id="login-username" placeholder="Tên đăng nhập hoặc email">
            </div>
            <div class="register--layout">
                <img src="{{ $fe('images/icon-pw.png') }}" alt="" class="popup-icon">
                <input type="password" id="login-password" placeholder="Mật khẩu">
            </div>
            <div class="forgotpass"><a href="{{ route('password.request') }}">Bạn quên mật khẩu?</a></div>
            <p id="login-message" style="display:none; margin:.5vw 0 0; font-size:.85vw; font-weight:600;"></p>
            <div class="submit">
                <a href="javascript:void(0);" id="btn-login-submit" class="cusor bright-12">ĐĂNG NHẬP</a>
            </div>
        </div>
    </div>
</div>

<script>
    var AUTH_POPUP_CONFIG = {
        sendOtpUrl: '{{ route('otp.send') }}',
        verifyOtpUrl: '{{ route('otp.verify') }}',
        registerUrl: '{{ route('register.popup') }}',
        loginUrl: '{{ route('login.popup') }}',
        csrfToken: '{{ csrf_token() }}',
        otpResendSeconds: 60,
        minAge: 18
    };
</script>
