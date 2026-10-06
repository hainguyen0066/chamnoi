<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PageVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrafficAnalyticsController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $sevenDaysAgo = Carbon::today()->subDays(6)->toDateString();
        $thirtyDaysAgo = Carbon::today()->subDays(29)->toDateString();

        // 1. Tổng quan lượt xem
        $totalPageviews = PageVisit::count();
        $todayPageviews = PageVisit::where('visited_date', $today)->count();
        $yesterdayPageviews = PageVisit::where('visited_date', $yesterday)->count();

        // Unique IPs
        $totalUniqueVisitors = PageVisit::distinct('ip_address')->count('ip_address');
        $todayUniqueVisitors = PageVisit::where('visited_date', $today)->distinct('ip_address')->count('ip_address');

        // 2. Biểu đồ Traffic 7 ngày gần nhất
        $dailyVisitsRaw = PageVisit::where('visited_date', '>=', $sevenDaysAgo)
            ->select('visited_date', DB::raw('count(*) as total'))
            ->groupBy('visited_date')
            ->pluck('total', 'visited_date')
            ->toArray();

        $dailyStats = [];
        $maxDailyCount = 1;
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i)->toDateString();
            $label = Carbon::today()->subDays($i)->format('d/m');
            $count = $dailyVisitsRaw[$d] ?? 0;
            if ($count > $maxDailyCount) $maxDailyCount = $count;
            $dailyStats[] = [
                'date' => $d,
                'label' => $label,
                'count' => $count,
            ];
        }

        // 3. Phân bổ thiết bị (Mobile vs Desktop)
        $deviceStats = PageVisit::select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        $mobileCount = $deviceStats['mobile'] ?? 0;
        $desktopCount = $deviceStats['desktop'] ?? 0;
        $tabletCount = $deviceStats['tablet'] ?? 0;
        $totalDevices = max(1, $mobileCount + $desktopCount + $tabletCount);

        // 4. Top Referrer (Nguồn truy cập: Google, Facebook, Zalo, Direct)
        $referrers = PageVisit::select('referrer_domain', DB::raw('count(*) as total'))
            ->groupBy('referrer_domain')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 5. Top bài viết & trang được xem nhiều nhất
        $topArticles = Article::orderByDesc('views_count')->limit(8)->get();

        $topUrls = PageVisit::select('url', DB::raw('count(*) as visits'))
            ->groupBy('url')
            ->orderByDesc('visits')
            ->limit(8)
            ->get();

        // 6. Phân tích lượt Click vào các tính năng & liên kết kiếm tiền
        $totalClicks = \App\Models\ClickEvent::count();
        $todayClicks = \App\Models\ClickEvent::where('event_date', $today)->count();
        $affiliateClicks = \App\Models\ClickEvent::where('event_name', 'affiliate_click')->count();
        $audioClicks = \App\Models\ClickEvent::where('event_name', 'play_audio')->count();
        $testClicks = \App\Models\ClickEvent::where('event_name', 'click_test')->count();
        $shareClicks = \App\Models\ClickEvent::where('event_name', 'social_share')->count();
        $spinClicks = \App\Models\ClickEvent::where('event_name', 'spin_wheel')->count();

        $topClickEvents = \App\Models\ClickEvent::select('event_label', 'event_name', DB::raw('count(*) as total'))
            ->groupBy('event_label', 'event_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // 7. Nhật ký 15 lượt truy cập gần đây (Live Feed)
        $recentVisits = PageVisit::latest()->limit(15)->get();

        return view('admin.traffic.index', compact(
            'totalPageviews',
            'todayPageviews',
            'yesterdayPageviews',
            'totalUniqueVisitors',
            'todayUniqueVisitors',
            'dailyStats',
            'maxDailyCount',
            'mobileCount',
            'desktopCount',
            'tabletCount',
            'totalDevices',
            'referrers',
            'topArticles',
            'topUrls',
            'totalClicks',
            'todayClicks',
            'affiliateClicks',
            'audioClicks',
            'testClicks',
            'shareClicks',
            'spinClicks',
            'topClickEvents',
            'recentVisits'
        ));
    }
}
