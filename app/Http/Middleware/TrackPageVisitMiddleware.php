<?php

namespace App\Http\Middleware;

use App\Models\Article;
use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ track GET requests, bỏ qua admin, api, sitemap, robots và static files
        if ($request->isMethod('GET') 
            && !$request->is('admin*') 
            && !$request->is('build*') 
            && !$request->is('images*')
            && !$request->is('sitemap.xml')
            && !$request->is('robots.txt')
            && !preg_match('/\.(ico|png|jpg|jpeg|gif|svg|webp|css|js|map|json|txt|xml)$/i', $request->path())
        ) {
            try {
                $this->logVisit($request);
            } catch (\Throwable $e) {
                // Ignore silently so it never interrupts the parent page
            }
        }

        return $response;
    }

    private function logVisit(Request $request): void
    {
        $userAgent = $request->userAgent() ?? '';
        $deviceType = 'desktop';

        if (preg_match('/(android|iphone|ipad|mobile|touch)/i', $userAgent)) {
            $deviceType = preg_match('/(ipad|tablet)/i', $userAgent) ? 'tablet' : 'mobile';
        }

        $referer = $request->headers->get('referer');
        $referrerDomain = 'direct';

        if ($referer) {
            $host = parse_url($referer, PHP_URL_HOST);
            if ($host) {
                if (str_contains($host, 'google.')) $referrerDomain = 'Google Search';
                elseif (str_contains($host, 'facebook.') || str_contains($host, 'fb.')) $referrerDomain = 'Facebook';
                elseif (str_contains($host, 'zalo.')) $referrerDomain = 'Zalo';
                elseif (str_contains($host, 'tiktok.')) $referrerDomain = 'TikTok';
                elseif (str_contains($host, 'youtube.')) $referrerDomain = 'YouTube';
                else $referrerDomain = $host;
            }
        }

        $articleId = null;
        $route = $request->route();
        if ($route && $route->getName() === 'articles.show') {
            $slug = $route->parameter('slug');
            if ($slug) {
                $article = Article::where('slug', $slug)->first();
                if ($article) {
                    $articleId = $article->id;
                    $article->increment('views_count');
                }
            }
        }

        PageVisit::create([
            'url' => substr($request->fullUrl(), 0, 255),
            'route_name' => $route ? $route->getName() : null,
            'article_id' => $articleId,
            'ip_address' => $request->ip(),
            'device_type' => $deviceType,
            'referrer_domain' => $referrerDomain,
            'user_agent' => substr($userAgent, 0, 255),
            'visited_date' => now()->toDateString(),
        ]);
    }
}
