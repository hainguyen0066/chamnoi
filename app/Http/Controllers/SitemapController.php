<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $articles = Article::where('is_published', true)->orderBy('updated_at', 'desc')->get();

        $staticPages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => now()->toIso8601String()],
            ['url' => route('vocabulary.index'), 'priority' => '0.95', 'changefreq' => 'daily', 'lastmod' => now()->toIso8601String()],
            ['url' => route('behavior-assessment.index'), 'priority' => '0.9', 'changefreq' => 'weekly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('articles.compare'), 'priority' => '0.9', 'changefreq' => 'weekly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('roadmap.index'), 'priority' => '0.85', 'changefreq' => 'monthly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('screening.index'), 'priority' => '0.85', 'changefreq' => 'monthly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('milestones.index'), 'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('flashcards.index'), 'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('medical-centers.index'), 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => now()->toIso8601String()],
            ['url' => route('articles.index'), 'priority' => '0.85', 'changefreq' => 'daily', 'lastmod' => now()->toIso8601String()],
        ];

        $xml = view('sitemap', compact('articles', 'staticPages'))->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Allow: /\n\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
