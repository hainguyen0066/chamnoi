{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($staticPages as $page)
        <url>
            <loc>{{ $page['url'] }}</loc>
            <lastmod>{{ $page['lastmod'] }}</lastmod>
            <changefreq>{{ $page['changefreq'] }}</changefreq>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach

    @foreach($articles as $article)
        <url>
            <loc>{{ route('articles.show', $article->slug) }}</loc>
            <lastmod>{{ $article->updated_at->toIso8601String() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
