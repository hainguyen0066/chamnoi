{{--
    Trang chủ. Nhận từ HomeController:
      $newsTabs : Collection Category (kèm posts mới nhất) cho khối tab tin tức
      $sliders  : Collection Slider zone home_hero cho khối "tính năng đặc sắc"
--}}
@extends('layouts.frontend')

@section('content')
    @php $fe = fn (string $path) => asset('frontend/assets/' . $path); @endphp

    <div class="figuretop"><img src="{{ $fe('images/mainfigure.png') }}" alt=""></div>

    <div class="slogan"><img src="{{ $fe('images/slogan.png') }}" alt=""></div>

    {{-- Cụm nút Đăng ký / Nạp thẻ / tải game --}}
    <div class="main-menu">
        <div class="menu-bg">
            <div class="layout-flex">
                <div class="menu--dky cpointer pc {{ auth()->guest() ? 'btn-register' : '' }}">
                    <a href="{{ auth()->guest() ? 'javascript:void(0);' : route('deposit.create') }}"><img src="{{ $fe('images/menutop-dk.png') }}"></a>
                </div>
                <div class="menu--napthe cpointer pc">
                    <a href="{{ route('deposit.create') }}"><img src="{{ $fe('images/menutop-napthe.png') }}"></a>
                </div>
                <div class="menu--icon">
                    <ul>
                        <li><a href="#"><img src="{{ $fe('images/menutop-apps.png') }}"></a></li>
                        <li><a href="#"><img src="{{ $fe('images/menutop-ggplay.png') }}"></a></li>
                        <li><a href="#"><img src="{{ $fe('images/menutop-apk.png') }}" alt=""></a></li>
                        <li><a href="#"><img src="{{ $fe('images/menutop-gialap.png') }}"></a></li>
                    </ul>
                </div>
                <div class="menu--dkymb">
                    <ul class="mb">
                        <li class="{{ auth()->guest() ? 'btn-register' : '' }}"><a href="{{ auth()->guest() ? 'javascript:void(0);' : route('deposit.create') }}"><img src="{{ $fe('images/menutop-dkmb.png') }}"></a></li>
                        <li><a href="{{ route('deposit.create') }}"><img src="{{ $fe('images/menutop-napthemb.png') }}"></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="menu-social pc">
            <div class="label18--home"><img src="{{ $fe('images/label18.png') }}"></div>
            <div class="mainmenu--icon">
                <ul>
                    <li><a href="{{ \App\Models\Setting::get('social_fanpage') ?: '#' }}" target="_blank" rel="noopener"><img src="{{ $fe('images/menutop-iconfb.png') }}"></a></li>
                    <li><a href="{{ \App\Models\Setting::get('social_zalo') ?: '#' }}" target="_blank" rel="noopener"><img src="{{ $fe('images/menutop-icongroup.png') }}"></a></li>
                    <li><a href="{{ \App\Models\Setting::get('social_youtube') ?: '#' }}" target="_blank" rel="noopener"><img src="{{ $fe('images/menutop-iconutube.png') }}"></a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Bảng tin: tab theo danh mục, bài viết lấy từ DB --}}
    <div class="news">
        <div class="news-bg">
            <div class="newslayout">
                <div class="new--img">
                    <a><img src="{{ $fe('images/bangtt-khunghinh1.png') }}"></a>
                </div>
                <div class="news--content">
                    <div class='content-daily'>
                        <div class='daily-detail'>
                            <ul class='menu-tab'>
                                @foreach ($newsTabs as $tab)
                                    <li class="{{ $loop->first ? 'active' : '' }}">
                                        <a href="javascript:void(0);">
                                            <span>{{ $tab->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class='content-tab'>
                                @foreach ($newsTabs as $tab)
                                    <div class="tab {{ $loop->first ? 'active' : '' }}">
                                        <div class='row'>
                                            <div class='daily-right'>
                                                <div class='content-detail'>
                                                    <div>
                                                        @forelse ($tab->posts as $post)
                                                            <div class='daily-content-text'>
                                                                <a href="{{ route('news.show', $post->slug) }}">
                                                                    <p>{{ $post->title }}</p>
                                                                    <span>{{ $post->published_at?->format('d/m/Y') }}</span>
                                                                </a>
                                                            </div>
                                                        @empty
                                                            <div class='daily-content-text'>
                                                                <a><p>Chưa có bài viết nào.</p><span></span></a>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="daily-seemore">
                            <a href="{{ route('news.index') }}">Xem toàn bộ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Giới thiệu nhân vật (dữ liệu tĩnh, sửa trong partial _characters) --}}
    @include('frontend.partials._characters')

    {{-- Tính năng đặc sắc: slider quản lý từ admin (zone home_hero).
         Cần tối thiểu 5 slide để CSS sprev/snext/sprev2/snext2 hoạt động đúng.
         Nếu admin chưa đủ slide, pad thêm bằng ảnh demo. --}}
    @php
        $sliderPad = max(0, 5 - $sliders->count());
    @endphp
    <div class="tinhnang">
        <div>
            <div class="title"><img src="{{ $fe('images/title-tinhnang.png') }}"></div>
            <div class="rev_slider">
                @foreach ($sliders as $slider)
                    <div class="rev_slide">
                        <div class="slide-img">
                            @if ($slider->url)
                                <a href="{{ $slider->url }}"><img src="{{ Storage::disk('public')->url($slider->image) }}" alt="{{ $slider->title }}"></a>
                            @else
                                <img src="{{ Storage::disk('public')->url($slider->image) }}" alt="{{ $slider->title }}">
                            @endif
                        </div>
                    </div>
                @endforeach
              
            </div>
        </div>
    </div>
@endsection
