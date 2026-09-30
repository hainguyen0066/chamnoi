{{--
    Kết quả thanh toán VNPay. Nhận từ PaymentCallbackController::vnpayReturn:
      $valid   : bool — chữ ký hợp lệ
      $success : bool — giao dịch thành công
      $deposit : ?Deposit
--}}
@extends('layouts.frontend')

@section('title', 'Kết quả thanh toán - ' . \App\Models\Setting::get('site_name', 'Tình Trong Thiên Hạ'))
@section('body-class', 'bg-detail')

@section('content')
    <div class="logo-mb pc"><img src="{{ asset('frontend/assets/images/logo-large.png') }}"></div>
    <div class="label18"><img src="{{ asset('frontend/assets/images/label18.png') }}"></div>

    <div style="text-align:center; padding:3vw 1vw;">
        @if (! $valid)
            <div style="font-size:3vw; margin-bottom:1vw;">⚠️</div>
            <h2 style="color:#c0392b; font-size:1.4vw; margin-bottom:.8vw;">Dữ liệu không hợp lệ</h2>
            <p style="color:#f5d07a; font-size:.95vw;">Chữ ký thanh toán không đúng, vui lòng liên hệ hỗ trợ.</p>
        @elseif ($success)
            <div style="font-size:3vw; margin-bottom:1vw;">✅</div>
            <h2 style="color:#2ecc71; font-size:1.4vw; margin-bottom:.8vw;">Thanh toán thành công!</h2>
            @if ($deposit)
                <p style="color:#f5d07a; font-size:1vw;">
                    Đã nạp <strong>{{ number_format($deposit->amount) }} đ</strong>
                    — nhận <strong style="color:#f1c40f;">{{ number_format($deposit->amount_received) }} xu</strong>.
                </p>
            @endif
        @else
            <div style="font-size:3vw; margin-bottom:1vw;">❌</div>
            <h2 style="color:#c0392b; font-size:1.4vw; margin-bottom:.8vw;">Thanh toán không thành công</h2>
            <p style="color:#f5d07a; font-size:.95vw;">Giao dịch bị huỷ hoặc bị từ chối. Chưa trừ tiền thì không sao cả.</p>
        @endif

        <div style="margin-top:2vw; display:flex; gap:1vw; justify-content:center;">
            <a href="{{ route('deposit.create') }}"
               style="padding:.6vw 1.5vw; background:linear-gradient(180deg,#e8b04a,#c07820); border-radius:8px; color:#fff; font-weight:700; font-size:.9vw; text-decoration:none;">
                Nạp thêm
            </a>
            <a href="{{ url('/') }}"
               style="padding:.6vw 1.5vw; background:rgba(255,255,255,.15); border:1.5px solid #E9B164; border-radius:8px; color:#f5d07a; font-size:.9vw; text-decoration:none;">
                Trang chủ
            </a>
        </div>
    </div>

    <div class="footer-detail"></div>
@endsection
