{{--
    Trang danh sách tin tức (tintuc.html). Nhận từ NewsController@index:
      $posts, $categories, $currentCategory
--}}
@extends('layouts.frontend')

@section('title', 'Tin Tức - ' . \App\Models\Setting::get('site_name', ''))
@section('body-class', 'bg-detail')

@section('content')
    <div class="logo-mb"><img src="{{ asset('frontend/assets/images/logo-large.png') }}"></div>
    <div class="label18"><img src="{{ asset('frontend/assets/images/label18.png') }}"></div>

    <div class="">
        <div class="title">
            <h3>TIN TỨC</h3>
        </div>
        <div class="tintuc">
            <div class="">
                @include('frontend.partials._news_nav')

                <main class="content">
                    <div>
                        @forelse ($posts as $post)
                            <div class="content-tintuc">
                                <a href="{{ route('news.show', $post->slug) }}">
                                    <p>{{ $post->title }}</p>
                                    <span>{{ $post->published_at?->format('d/m/Y') }}</span>
                                </a>
                            </div>
                        @empty
                            <div class="content-tintuc">
                                <a><p>Không tìm thấy bài viết nào.</p></a>
                            </div>
                        @endforelse
                    </div>

                    {{ $posts->links('frontend.partials._pagination') }}
                </main>
            </div>
        </div>
    </div>

    <div class="footer-detail"></div>
@endsection
