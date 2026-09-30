{{--
    Thanh điều hướng khu tin tức (dùng chung cho trang danh sách + chi tiết):
    tab danh mục + ô tìm kiếm. Nhận:
      $categories      : Collection Category hiển thị làm tab
      $currentCategory : slug danh mục đang chọn (null = tất cả)
--}}
<ul class="menu-tab">
    <li class="{{ empty($currentCategory) ? 'active' : '' }}">
        <a href="{{ route('news.index') }}">
            <span>Tất Cả</span>
        </a>
    </li>
    @foreach ($categories as $category)
        <li class="{{ ($currentCategory ?? null) === $category->slug ? 'active' : '' }}">
            <a href="{{ route('news.index', ['danh-muc' => $category->slug]) }}">
                <span>{{ $category->name }}</span>
            </a>
        </li>
    @endforeach
</ul>

<form class="search-bar" method="GET" action="{{ route('news.index') }}">
    @if (!empty($currentCategory))
        <input type="hidden" name="danh-muc" value="{{ $currentCategory }}">
    @endif
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm..." class="search-input">
    <button class="search-btn" type="submit">
        <img src="{{ asset('frontend/assets/images/search.png') }}" alt="Search">
    </button>
</form>
