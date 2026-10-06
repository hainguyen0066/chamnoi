@extends('layouts.app')

@section('title', 'Cẩm Nang Tri Thức Về Chậm Nói Ở Trẻ - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Thư viện tri thức
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Cẩm Nang Chăm Con Chậm Nói
            </h1>
            <p class="text-slate-600 text-sm sm:text-base">
                Tổng hợp bài viết khoa học từ các bác sĩ chuyên khoa Nhi & chuyên viên âm ngữ trị liệu, giúp cha mẹ thấu hiểu và đồng hành cùng con mỗi ngày.
            </p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200/80 mb-10">
            <form method="GET" action="{{ route('articles.index') }}" class="space-y-4">
                <!-- Search input -->
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm bài viết (ví dụ: màn hình điện thoại, tự kỷ, nguyên tắc 3T, dính thắng lưỡi...)" class="w-full pl-11 pr-4 py-3 text-sm rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>

                <!-- Category pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-semibold">
                    <a href="{{ route('articles.index', array_merge(request()->except('category', 'page'))) }}" class="px-4 py-2 rounded-xl shrink-0 transition {{ empty($selectedCategory) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Tất cả chủ đề
                    </a>
                    @foreach($categories as $key => $name)
                        <a href="{{ route('articles.index', array_merge(request()->except('category', 'page'), ['category' => $key])) }}" class="px-4 py-2 rounded-xl shrink-0 transition {{ $selectedCategory === $key ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Articles Grid -->
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($articles as $article)
                    <article class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200/80 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider
                                    @if($article->badge_color === 'rose') bg-rose-100 text-rose-800
                                    @elseif($article->badge_color === 'purple') bg-purple-100 text-purple-800
                                    @elseif($article->badge_color === 'amber') bg-amber-100 text-amber-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    {{ $article->badge_text ?? $article->category_label }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">{{ $article->reading_time }}</span>
                            </div>

                            <h2 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition font-heading mb-3 line-clamp-2">
                                <a href="{{ route('articles.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            <p class="text-slate-600 text-sm leading-relaxed line-clamp-3 mb-6">
                                {{ $article->excerpt }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-medium text-slate-600">{{ $article->category_label }}</span>
                            <a href="{{ route('articles.show', $article->slug) }}" class="font-bold text-emerald-600 group-hover:translate-x-1 transition flex items-center gap-1">
                                Đọc chi tiết &rarr;
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div>
                {{ $articles->links() }}
            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 max-w-lg mx-auto">
                <div class="text-4xl mb-3">🔍</div>
                <h3 class="text-lg font-bold text-slate-900 font-heading mb-2">Không tìm thấy bài viết phù hợp</h3>
                <p class="text-sm text-slate-500 mb-6">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc danh mục.</p>
                <a href="{{ route('articles.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-sm">
                    Xem tất cả bài viết
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
