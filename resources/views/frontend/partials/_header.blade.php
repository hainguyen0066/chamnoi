{{--
    Header dùng chung: menu dọc bên phải (PC), menu ngang trên cùng (PC),
    menu mobile. Link social đọc từ Settings để admin đổi không cần sửa code.
    Nút Đăng ký/Đăng nhập trỏ thẳng về trang auth của hệ thống.
--}}
@php
    $socialFanpage = \App\Models\Setting::get('social_fanpage') ?: '#';
    $socialYoutube = \App\Models\Setting::get('social_youtube') ?: '#';
    $socialZalo = \App\Models\Setting::get('social_zalo') ?: '#';
    $fe = fn (string $path) => asset('frontend/assets/' . $path);
@endphp

{{-- Menu dọc bên phải (PC) --}}
<div class="menuright pc">
    <div class="menuright-bg">
        <div class="menuright--icon">
            <ul>
                <li><a href="{{ $socialFanpage }}" target="_blank" rel="noopener"><img src="{{ $fe('images/mr-fb.png') }}"></a></li>
                <li><a href="{{ $socialZalo }}" target="_blank" rel="noopener"><img src="{{ $fe('images/mr-group.png') }}"></a></li>
                <li><a href="{{ $socialYoutube }}" target="_blank" rel="noopener"><img src="{{ $fe('images/mr-utube.png') }}"></a></li>
            </ul>
        </div>

        <div class="menuright--apps">
            <ul>
                <li><a href="javascript:void(0)"><img src="{{ $fe('images/mr-apps.png') }}"></a></li>
                <li><a href="javascript:void(0)"><img src="{{ $fe('images/mr-ggplay.png') }}"></a></li>
                <li><a href="javascript:void(0)"><img src="{{ $fe('images/mr-apk.png') }}"></a></li>
                <li><a href="javascript:void(0)"><img src="{{ $fe('images/mr-gialap.png') }}"></a></li>
            </ul>
        </div>
        <div class="menuright--nap">
            <ul>
                @auth
                    <li>
                        <div style="padding: 8px 6px; text-align: center; line-height: 1.5;">
                            <span style="color: #f5d07a; font-size: 12px; font-weight: bold; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80px;">{{ auth()->user()->name }}</span>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('mr-logout-form').submit();"
                               style="color: #e8c060; font-size: 11px; text-decoration: none;">
                                Thoát
                            </a>
                        </div>
                        <form id="mr-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                    </li>
                @else
                    <li class="btn-register cpointer"><a href="javascript:void(0);"><img src="{{ $fe('images/mr-dk.png') }}"></a></li>
                @endauth
                    <li><a href="{{ route('deposit.create') }}"><img src="{{ $fe('images/mr-napthe.png') }}"></a></li>
            </ul>
        </div>
        <div class="top" id="return-to-top">
            <img src="{{ $fe('images/arrow.png') }}">
        </div>
        <div class="r-close-menu"><a href="javascript:void(0);"><img src="{{ $fe('images/mr-hide.png') }}" alt=""></a>
        </div>
    </div>
</div>

{{-- Menu ngang trên cùng (PC) --}}
<div class="menutop pc">
    <div class="logo"><a href="{{ url('/') }}"><img src="{{ $fe('images/avt.png') }}"></a></div>
    <div class="menu-bg">
        <ul>
            <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ url('/') }}">
                    <img src="{{ $fe('images/menu-trangchu.png') }}" style="transform: translateX(-2.145vw);">
                </a></li>
            <li class="{{ request()->routeIs('news.*') ? 'active' : '' }}"><a href="{{ route('news.index') }}">
                    <img src="{{ $fe('images/menu-sukien.png') }}" style="transform: translateX(-0.5vw);">
                </a></li>
            <li><a href="{{ route('news.index') }}">
                    <img src="{{ $fe('images/menu-camnang.png') }}" style="transform: translateX(0);">
                </a></li>
            <li><a href="{{ route('deposit.create') }}">
                    <img src="{{ $fe('images/menu-napthe.png') }}" style="transform: translateX(0.75vw);">
                </a></li>
            <li><a href="{{ $socialFanpage }}" target="_blank" rel="noopener">
                    <img src="{{ $fe('images/menu-congdong.png') }}" style="transform: translateX(1vw);">
                </a></li>
            <li><a href="{{ $socialFanpage }}" target="_blank" rel="noopener">
                    <img src="{{ $fe('images/menu-hotro.png') }}" style="transform: translateX(2.15vw);">
                </a></li>
        </ul>
    </div>
    <div class="logo-cty">
        <img src="{{ $fe('images/logo.png') }}">
    </div>
</div>

{{-- Menu mobile --}}
<div class="mbmenutop mb">
    <div>
        <div class="mainmenu--icon">
            <ul>
                <li><a href="{{ $socialFanpage }}" target="_blank" rel="noopener"><img src="{{ $fe('images/menutop-iconfb.png') }}"></a></li>
                <li><a href="{{ $socialZalo }}" target="_blank" rel="noopener"><img src="{{ $fe('images/menutop-icongroup.png') }}"></a></li>
                <li><a href="{{ $socialYoutube }}" target="_blank" rel="noopener"><img src="{{ $fe('images/menutop-iconutube.png') }}"></a></li>
            </ul>
        </div>
        <div class="iconmenu main-header-right"><a><img src="{{ $fe('images/icon-menu.png') }}"></a></div>
        <div class="menuclose"><a><img src="{{ $fe('images/close.png') }}"></a></div>
        <div class="menu-right mobile">
            <ul>
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ url('/') }}">
                        <img src="{{ $fe('images/menu-trangchu.png') }}" style="transform: translateX(-2.145vw);">
                    </a></li>
                <li class="{{ request()->routeIs('news.*') ? 'active' : '' }}"><a href="{{ route('news.index') }}">
                        <img src="{{ $fe('images/menu-sukien.png') }}" style="transform: translateX(-0.5vw);">
                    </a></li>
                <li><a href="{{ route('news.index') }}">
                        <img src="{{ $fe('images/menu-camnang.png') }}" style="transform: translateX(0);">
                    </a></li>
                <li><a href="{{ route('deposit.create') }}">
                        <img src="{{ $fe('images/menu-napthe.png') }}" style="transform: translateX(0.75vw);">
                    </a></li>
                <li><a href="{{ $socialFanpage }}" target="_blank" rel="noopener">
                        <img src="{{ $fe('images/menu-congdong.png') }}" style="transform: translateX(1vw);">
                    </a></li>
                <li><a href="{{ $socialFanpage }}" target="_blank" rel="noopener">
                        <img src="{{ $fe('images/menu-hotro.png') }}" style="transform: translateX(2.15vw);">
                    </a></li>
            </ul>
        </div>
    </div>
</div>
