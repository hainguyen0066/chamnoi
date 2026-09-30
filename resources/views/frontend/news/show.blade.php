{{--
    Trang chi tiết tin tức (chitiet-tintuc.html). Nhận từ NewsController@show:
      $post, $categories, $currentCategory
--}}
@extends('layouts.frontend')

@section('title', ($post->meta_title ?: $post->title) . ' - ' . \App\Models\Setting::get('site_name', ''))
@section('meta_description', $post->meta_description ?: $post->excerpt)
@if ($post->thumbnail)
    @section('meta_image', Storage::disk('public')->url($post->thumbnail))
@endif
@section('body-class', 'bg-detail')

@section('content')
    <div class="logo-mb pc"><img src="{{ asset('frontend/assets/images/logo-large.png') }}"></div>
    <div class="label18"><img src="{{ asset('frontend/assets/images/label18.png') }}"></div>

    <div class="">
        <div class="title">
            <h3>CHI TIẾT TIN TỨC</h3>
        </div>
        <div class="tintuc">
            <div class="">
                @include('frontend.partials._news_nav')

                <main class="content">
                    <h3 class="main-title">{{ $post->title }}</h3>

                    {{-- Nội dung HTML soạn từ TinyMCE trong admin --}}
                    <section class="section">
                        {!! $post->content !!}
                    </section>

                    <p style="opacity:.6; font-size:13px; margin-top:2em;">
                        Đăng ngày {{ $post->published_at?->format('d/m/Y') }}
                        @if ($post->categories->isNotEmpty())
                            — chuyên mục:
                            @foreach ($post->categories as $category)
                                <a href="{{ route('news.index', ['danh-muc' => $category->slug]) }}">{{ $category->name }}</a>@if(!$loop->last), @endif
                            @endforeach
                        @endif
                    </p>
                </main>
            </div>
        </div>
    </div>

    <div class="footer-detail"></div>
@endsection
