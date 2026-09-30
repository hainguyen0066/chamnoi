{{--
    Layout chung cho toàn bộ trang frontend (giao diện game Tình Trong Thiên Hạ).

    Cách dùng trong trang con:
        @extends('layouts.frontend')
        @section('title', 'Tiêu đề trang')
        @section('body-class', 'bg-detail')   <- trang trong; trang chủ bỏ trống
        @section('content') ... @endsection

    Phần dùng lại nhiều lần tách thành partial trong resources/views/frontend/partials:
        _header.blade.php  - menu phải + menu top PC + menu mobile
        _footer.blade.php  - footer (thông tin sản phẩm, social, pháp lý)
--}}
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('site_description', ''))">
    @hasSection('meta_image')
        <meta property="og:image" content="@yield('meta_image')">
    @endif
    <meta property="og:title" content="@yield('title', \App\Models\Setting::get('site_name', 'Tình Trong Thiên Hạ'))">
    <link rel="shortcut icon" id="favicon" href="{{ asset('frontend/assets/images/avt.png') }}">
    <title>@yield('title', \App\Models\Setting::get('site_name', 'Tình Trong Thiên Hạ'))</title>

    <!-- Framework core CSS -->
    <link href="{{ asset('frontend/assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/css/slick.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('frontend/assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/css/responsive.css') }}" rel="stylesheet">
</head>

<body class="@yield('body-class')">
    <div id="mask"></div>

    @include('frontend.partials._header')

    <div class="wrapper">
        <div class="background">
            <div class="container">

                @yield('content')

                @include('frontend.partials._footer')
            </div>
        </div>
    </div>

    {{-- Popup đăng nhập / đăng ký (guest) — mở bằng .btn-register / .btn-login --}}
    @guest
        @include('frontend.partials._popups')
    @endguest

    <!-- ============================ -->
    <!-- script -->
    <script type="text/javascript" src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>
    @guest
        <script src="{{ asset('frontend/assets/js/register-popup.js') }}"></script>
    @endguest
    @stack('scripts')
</body>

</html>
