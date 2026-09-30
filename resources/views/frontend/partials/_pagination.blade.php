{{--
    Phân trang theo đúng markup .pagination của template gốc.
    Dùng: {{ $posts->links('frontend.partials._pagination') }}
--}}
@if ($paginator->hasPages())
    <div class="pagination">
        {{-- Nút về trang trước --}}
        @if ($paginator->onFirstPage())
            <a>«</a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">«</a>
        @endif

        {{-- Các số trang: hiện trang đầu/cuối + 2 trang quanh trang hiện tại --}}
        @foreach (range(1, $paginator->lastPage()) as $page)
            @if ($page === $paginator->currentPage())
                <a class="active">{{ $page }}</a>
            @elseif (abs($page - $paginator->currentPage()) <= 2 || $page === 1 || $page === $paginator->lastPage())
                <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
            @elseif (abs($page - $paginator->currentPage()) === 3)
                <a>...</a>
            @endif
        @endforeach

        {{-- Nút sang trang sau --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">»</a>
        @else
            <a>»</a>
        @endif
    </div>
@endif
