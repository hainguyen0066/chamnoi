@extends('layouts.app')

@section('title', $article->seo_title)
@section('meta_description', $article->seo_description)

@section('meta_tags')
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $article->seo_title }}">
    <meta name="keywords" content="{{ $article->meta_keywords ?? 'trẻ chậm nói, chậm nói đơn thuần, tự kỷ, can thiệp âm ngữ, kích âm tại nhà' }}">
    <meta name="author" content="Mầm Ngôn Ngữ">

    <!-- Open Graph / Facebook / Zalo -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('articles.show', $article->slug) }}">
    <meta property="og:title" content="{{ $article->seo_title }}">
    <meta property="og:description" content="{{ $article->seo_description }}">
    <meta property="og:image" content="{{ $article->og_image ?? url('/logo.png') }}">
    <meta property="article:published_time" content="{{ $article->created_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
    <meta property="article:section" content="{{ $article->category_label }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ route('articles.show', $article->slug) }}">
    <meta property="twitter:title" content="{{ $article->seo_title }}">
    <meta property="twitter:description" content="{{ $article->seo_description }}">
    <meta property="twitter:image" content="{{ $article->og_image ?? url('/logo.png') }}">

    <!-- Schema.org JSON-LD (Google Rich Snippets) -->
    <script type="application/ld+json">
        {!! json_encode($article->json_ld_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- BreadcrumbList Schema -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Cẩm nang', 'item' => route('articles.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $article->title, 'item' => route('articles.show', $article->slug)],
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
<div class="bg-slate-50 py-10 lg:py-16" x-data="{ copied: false }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb Navigation -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('articles.index') }}" class="hover:text-emerald-600 transition">Cẩm nang</a>
            <span>/</span>
            <a href="{{ route('articles.index', ['category' => $article->category]) }}" class="hover:text-emerald-600 transition font-medium">{{ $article->category_label }}</a>
            <span>/</span>
            <span class="text-slate-800 font-bold truncate max-w-xs">{{ $article->title }}</span>
        </nav>

        <!-- Main Article Container -->
        <article class="bg-white rounded-3xl p-6 sm:p-12 shadow-sm border border-slate-200 mb-12">
            <!-- Header -->
            <header class="border-b border-slate-100 pb-8 mb-8">
                <div class="flex items-center gap-3 mb-4 flex-wrap">
                    <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider
                        @if($article->badge_color === 'rose') bg-rose-100 text-rose-800
                        @elseif($article->badge_color === 'purple') bg-purple-100 text-purple-800
                        @elseif($article->badge_color === 'amber') bg-amber-100 text-amber-800
                        @else bg-emerald-100 text-emerald-800 @endif">
                        {{ $article->badge_text ?? $article->category_label }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">⏱️ {{ $article->reading_time }}</span>
                    <span class="text-xs text-slate-400 font-medium">• Lượt xem: <strong>{{ number_format($article->views_count) }}</strong></span>
                    <span class="text-xs text-slate-400 font-medium">• Cập nhật: {{ $article->updated_at->format('d/m/Y') }}</span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 font-heading leading-tight mb-6">
                    {{ $article->title }}
                </h1>

                <!-- Author & Medical Editorial Board Info (E-E-A-T Signal for Google) -->
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                        MN
                    </div>
                    <div class="text-xs">
                        <div class="font-bold text-slate-800">Biên soạn bởi Ban Chuyên Môn Mầm Ngôn Ngữ</div>
                        <div class="text-slate-400">Tham vấn theo hướng dẫn của CDC Hoa Kỳ & Hiệp hội Âm ngữ Trị liệu</div>
                    </div>
                </div>

                <!-- Lead Excerpt -->
                <div class="p-4 sm:p-6 rounded-2xl bg-amber-50/70 border-l-4 border-amber-500 text-amber-950 font-medium text-sm sm:text-base leading-relaxed">
                    {{ $article->excerpt }}
                </div>
            </header>

            <!-- In-Article Ad Slot (Vị trí quảng cáo trong bài viết / AdSense) -->
            @php
                $adInArticleCode = $globalSiteSettings['ad_in_article_code'] ?? null;
                $adInArticleEnabled = ($globalSiteSettings['ad_in_article_enabled'] ?? '0') === '1';
            @endphp

            @if($adInArticleEnabled && !empty($adInArticleCode))
                <div class="my-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center overflow-hidden">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-2">Quảng cáo tài trợ</span>
                    {!! $adInArticleCode !!}
                </div>
            @endif

            <!-- Article Body -->
            <div class="prose prose-slate prose-lg max-w-none prose-headings:font-heading prose-headings:font-bold prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4 prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3 prose-p:leading-relaxed prose-p:text-slate-700 prose-ul:my-4 prose-li:my-1 text-slate-700">
                {!! $article->content !!}
            </div>

            <!-- Affiliate Product / Sponsorship Box (Kiếm tiền từ Affiliate/Sản phẩm liên kết) -->
            @php
                $affiliateCode = $globalSiteSettings['ad_sidebar_affiliate_code'] ?? null;
                $affiliateEnabled = ($globalSiteSettings['ad_sidebar_affiliate_enabled'] ?? '0') === '1';
            @endphp

            @if($affiliateEnabled && !empty($affiliateCode))
                <div class="mt-10 p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">🎁</span>
                        <h4 class="font-bold text-sm sm:text-base text-emerald-950 font-heading">Sản phẩm & Giáo cụ khuyên dùng cho bé</h4>
                    </div>
                    <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                        {!! $affiliateCode !!}
                    </div>
                </div>
            @endif

            <!-- Footer: Social Sharing & Quick Tools -->
            <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Social Share Buttons -->
                <div class="flex items-center gap-2 text-xs text-slate-600 flex-wrap">
                    <span class="font-bold">Chia sẻ bài viết:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition flex items-center gap-1 shadow-sm">
                        <span>Facebook</span>
                    </a>
                    <a href="https://zalo.me/share?url={{ urlencode(request()->fullUrl()) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="px-3 py-1.5 rounded-xl bg-sky-500 text-white font-bold hover:bg-sky-600 transition flex items-center gap-1 shadow-sm">
                        <span>Zalo</span>
                    </a>
                    <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)"
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition flex items-center gap-1">
                        <span x-text="copied ? '✓ Đã sao chép link' : '🔗 Copy link'"></span>
                    </button>
                </div>

                <!-- Action CTA -->
                <a href="{{ route('behavior-assessment.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 via-amber-500 to-emerald-600 hover:from-rose-600 hover:to-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center gap-1.5 shrink-0">
                    <span>🔬 Phân tích cờ đỏ của con</span>
                    &rarr;
                </a>
            </div>
        </article>

        <!-- Related Articles -->
        @if($relatedArticles->count() > 0)
            <div class="mt-8">
                <h3 class="text-xl font-bold text-slate-900 font-heading mb-6 flex items-center gap-2">
                    <span>📚</span> Bài viết cùng chủ đề cha mẹ nên đọc
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $rel)
                        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block mb-2">{{ $rel->category_label }}</span>
                                <h4 class="font-bold text-base text-slate-900 group-hover:text-emerald-600 transition font-heading line-clamp-2 mb-2">
                                    <a href="{{ route('articles.show', $rel->slug) }}">{{ $rel->title }}</a>
                                </h4>
                                <p class="text-xs text-slate-500 line-clamp-3 mb-4 leading-relaxed">{{ $rel->excerpt }}</p>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-100 pt-3">
                                <span>{{ $rel->reading_time }}</span>
                                <span class="text-emerald-600 font-bold group-hover:translate-x-1 transition">Đọc tiếp &rarr;</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
