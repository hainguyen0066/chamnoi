{{--
    Trang Nạp Thẻ (napthe.html). Nhận từ Frontend\DepositController@create:
      $promotionPercents : [0, 5, 10, 20]
      $vndPerXu          : 1000
--}}
@extends('layouts.frontend')

@section('title', 'Nạp Thẻ - ' . \App\Models\Setting::get('site_name', 'Tình Trong Thiên Hạ'))
@section('body-class', 'bg-detail')

@section('content')
    @php $fe = fn (string $path) => asset('frontend/assets/' . $path); @endphp

    <div class="logo-mb pc"><img src="{{ $fe('images/logo-large.png') }}"></div>
    <div class="label18"><img src="{{ $fe('images/label18.png') }}"></div>

    <div class="">
        <div class="title">
            <h3>THÔNG TIN NẠP THẺ</h3>
            <h4 style="color:#f5d07a; font-size:1vw; margin-top:.5vw;">Hướng dẫn Nạp Thẻ Tự Động</h4>
        </div>

        {{-- VNPay payment form --}}
        <div class="nap-the-box" style="max-width:56vw; margin:2vw auto 0;">
            <p style="font-weight:700; font-size:1.1vw; color:#703D00; margin-bottom:1vw;">
                💳 Thanh Toán Qua VNPay
            </p>

            @if (session('error'))
                <p style="color:#c00; font-size:.9vw; margin-bottom:.8vw;">{{ session('error') }}</p>
            @endif

            @auth
                <form method="POST" action="{{ route('deposit.store') }}" id="vnpay-form">
                    @csrf

                    {{-- Preset amount buttons --}}
                    <div style="display:flex; flex-wrap:wrap; gap:.5vw; margin-bottom:1vw;">
                        @php
                            $presets = [50000, 100000, 200000, 500000, 1000000, 5000000, 10000000, 50000000];
                        @endphp
                        @foreach ($presets as $preset)
                            <button type="button"
                                class="amount-btn"
                                data-value="{{ $preset }}"
                                style="padding:.4vw .8vw; background:#E9B164; border:1.5px solid #c8913a; border-radius:6px; cursor:pointer; font-size:.85vw; color:#4a1e00; font-weight:600;">
                                {{ number_format($preset / 1000) }}K
                            </button>
                        @endforeach
                    </div>

                    {{-- Custom amount input --}}
                    <div style="display:flex; align-items:center; gap:1vw; margin-bottom:.8vw;">
                        <input type="number"
                               name="amount"
                               id="vnpay-amount"
                               min="10000"
                               max="1000000000"
                               step="1000"
                               placeholder="Nhập số tiền (VND)..."
                               style="flex:1; padding:.5vw .8vw; border:1.5px solid #E9B164; border-radius:6px; font-size:.9vw; background:#FFFCED; color:#4a1e00;"
                               value="{{ old('amount') }}"
                               required>
                        <span style="font-size:.9vw; color:#703D00; white-space:nowrap;">VND</span>
                    </div>

                    @error('amount')
                        <p style="color:#c00; font-size:.85vw; margin-bottom:.5vw;">{{ $message }}</p>
                    @enderror

                    {{-- Xu preview --}}
                    <p style="font-size:.9vw; color:#4a1e00; margin-bottom:1vw;">
                        Nhận: <strong id="xu-preview" style="color:#c0392b; font-size:1.1vw;">—</strong> xu
                        <span id="promo-badge" style="display:none; font-size:.75vw; color:#27ae60; margin-left:.4vw;"></span>
                    </p>

                    <button type="submit"
                            style="width:100%; padding:.6vw; background:linear-gradient(180deg,#e8b04a,#c07820); border:none; border-radius:8px; color:#fff; font-weight:700; font-size:1vw; cursor:pointer; letter-spacing:.05em;">
                        🔐 Thanh Toán VNPay
                    </button>
                </form>
            @else
                <p style="font-size:.95vw; color:#703D00; text-align:center; padding:1vw 0;">
                    Vui lòng <a href="{{ route('login') }}" style="font-weight:700;">đăng nhập</a> để nạp xu qua VNPay.
                </p>
            @endauth
        </div>

        {{-- Bank transfer info (from napthe.html) --}}
        <div class="nap-the-box" style="max-width:56vw; margin:1.5vw auto 0;">
            <p style="font-weight:700; font-size:1.05vw; color:#703D00;">🏦 Nạp Thẻ Qua Chuyển Khoản Ngân Hàng</p>
            <p>Mỗi tài khoản có <strong>1 mã QR riêng biệt</strong>. Vui lòng đăng nhập vào game trước, vào mục Nạp Thẻ để lấy QR của mình. Có thể lưu lại để nạp tự động sau này.</p>
            <p>BQT không chịu trách nhiệm nếu quét sai QR.</p>
            <p>Ngân hàng: <strong>TK 1631888888 – ACB – Phạm Thị Lan Anh</strong></p>
            <p>Cú pháp chuyển khoản thủ công: <strong>NAP VOLAMCTC &lt;tài khoản&gt;</strong></p>
            @auth
                <p>Tài khoản của bạn: <strong style="color:#c0392b;">NAP VOLAMCTC {{ auth()->user()->name }}</strong></p>
            @endauth
            <p>Liên hệ hỗ trợ: <a href="{{ \App\Models\Setting::get('social_fanpage') ?: '#' }}" target="_blank" rel="noopener">Fanpage</a> hoặc hotline <strong>0907918388</strong></p>
        </div>

        {{-- QR + Discount table --}}
        <div class="nap-the-card" style="max-width:56vw; margin:1.5vw auto 0; display:grid; grid-template-columns:1fr 1fr; gap:2vw; padding:1.5vw; background:#FFFCED; border:2px solid #E9B164; border-radius:12px; position:relative; box-sizing:border-box;">
            {{-- Left: QR --}}
            <div style="text-align:center;">
                <p style="font-weight:700; color:#703D00; margin-bottom:.8vw;">Mã QR Ngân Hàng</p>
                <img src="{{ $fe('images/qr.png') }}" alt="QR Nạp Thẻ" style="max-width:12vw; border-radius:8px; border:1px solid #E9B164;">
                <div style="margin-top:.8vw; font-size:.8vw; color:#4a1e00; line-height:1.6;">
                    <p><strong>Ngân hàng:</strong> ACB</p>
                    <p><strong>Số TK:</strong> 1631888888</p>
                    <p><strong>Tên:</strong> PHẠM THỊ LAN ANH</p>
                    <p style="color:#c0392b;"><strong>Nội dung:</strong>
                        @auth
                            NAP VOLAMCTC {{ auth()->user()->name }}
                        @else
                            NAP VOLAMCTC &lt;tài khoản của bạn&gt;
                        @endauth
                    </p>
                </div>
            </div>

            {{-- Divider --}}
            <div style="position:absolute; left:50%; top:5%; height:90%; width:2px; background:#E9B164; transform:translateX(-50%);"></div>

            {{-- Right: Discount table --}}
            <div>
                <p style="font-weight:700; color:#703D00; margin-bottom:.8vw;">Bảng Ưu Đãi Nạp Thẻ</p>
                <table style="width:100%; font-size:.8vw; border-collapse:collapse; color:#4a1e00;">
                    <thead>
                        <tr style="background:#E9B164;">
                            <th style="padding:.4vw; border:1px solid #c8913a;">Số tiền nạp</th>
                            <th style="padding:.4vw; border:1px solid #c8913a;">Ưu đãi</th>
                            <th style="padding:.4vw; border:1px solid #c8913a;">Ví dụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:.4vw; border:1px solid #E9B164;">Dưới 1 triệu</td>
                            <td style="padding:.4vw; border:1px solid #E9B164; text-align:center;">0%</td>
                            <td style="padding:.4vw; border:1px solid #E9B164;">100k = 100 xu</td>
                        </tr>
                        <tr style="background:#FFFCE8;">
                            <td style="padding:.4vw; border:1px solid #E9B164;">1tr – 9.999.000đ</td>
                            <td style="padding:.4vw; border:1px solid #E9B164; text-align:center; color:#27ae60; font-weight:700;">+5%</td>
                            <td style="padding:.4vw; border:1px solid #E9B164;">1tr = 1.050 xu</td>
                        </tr>
                        <tr>
                            <td style="padding:.4vw; border:1px solid #E9B164;">10tr – 99.999.000đ</td>
                            <td style="padding:.4vw; border:1px solid #E9B164; text-align:center; color:#e67e22; font-weight:700;">+10%</td>
                            <td style="padding:.4vw; border:1px solid #E9B164;">10tr = 11.000 xu</td>
                        </tr>
                        <tr style="background:#FFFCE8;">
                            <td style="padding:.4vw; border:1px solid #E9B164;">Từ 100 triệu trở lên</td>
                            <td style="padding:.4vw; border:1px solid #E9B164; text-align:center; color:#c0392b; font-weight:700;">+20%</td>
                            <td style="padding:.4vw; border:1px solid #E9B164;">100tr = 120.000 xu</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size:.75vw; color:#703D00; margin-top:.8vw; font-style:italic;">
                    ⚠ Không tự sửa nội dung QR. Sau 1–2 phút nhận xu tại "Võ Lâm Kim Bài" trong game.
                </p>
            </div>
        </div>
    </div>

    <div class="footer-detail"></div>
@endsection

@push('scripts')
<script>
(function () {
    var VND_PER_XU = {{ $vndPerXu }};

    function calcPromotion(amount) {
        if (amount >= 100000000) return 20;
        if (amount >= 10000000)  return 10;
        if (amount >= 1000000)   return 5;
        return 0;
    }

    function calcXu(amount) {
        var promo = calcPromotion(amount);
        return Math.floor(amount * (1 + promo / 100) / VND_PER_XU);
    }

    function updatePreview() {
        var input  = document.getElementById('vnpay-amount');
        var preview = document.getElementById('xu-preview');
        var badge   = document.getElementById('promo-badge');
        if (!input || !preview) return;

        var amount = parseInt(input.value, 10);
        if (!amount || amount < 10000) {
            preview.textContent = '—';
            if (badge) badge.style.display = 'none';
            return;
        }

        var promo = calcPromotion(amount);
        preview.textContent = calcXu(amount).toLocaleString('vi-VN');
        if (badge) {
            if (promo > 0) {
                badge.textContent = '(+' + promo + '% ưu đãi)';
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var amountInput = document.getElementById('vnpay-amount');
        if (amountInput) {
            amountInput.addEventListener('input', updatePreview);
            updatePreview();
        }

        document.querySelectorAll('.amount-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var amountInput = document.getElementById('vnpay-amount');
                if (amountInput) {
                    amountInput.value = btn.getAttribute('data-value');
                    updatePreview();
                }
                document.querySelectorAll('.amount-btn').forEach(function (b) {
                    b.style.background = '#E9B164';
                });
                btn.style.background = '#c07820';
            });
        });
    });
})();
</script>
@endpush
