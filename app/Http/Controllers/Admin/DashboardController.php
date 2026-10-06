<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PageVisit;
use App\Models\ScreeningQuestion;
use App\Models\ScreeningSubmission;

class DashboardController extends Controller
{
    public function index()
    {
        $totalArticles = Article::count();
        $totalQuestions = ScreeningQuestion::count();
        $totalSubmissions = ScreeningSubmission::count();

        $totalTrafficViews = PageVisit::count();
        $todayTrafficViews = PageVisit::where('visited_date', now()->toDateString())->count();
        $totalUniqueVisitors = PageVisit::distinct('ip_address')->count('ip_address');

        $highRiskSubmissions = ScreeningSubmission::where('risk_level', 'high')->count();
        $mediumRiskSubmissions = ScreeningSubmission::where('risk_level', 'medium')->count();
        $lowRiskSubmissions = ScreeningSubmission::where('risk_level', 'low')->count();

        $recentSubmissions = ScreeningSubmission::latest()->take(5)->get();
        $topArticles = Article::orderByDesc('views_count')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalArticles',
            'totalQuestions',
            'totalSubmissions',
            'totalTrafficViews',
            'todayTrafficViews',
            'totalUniqueVisitors',
            'highRiskSubmissions',
            'mediumRiskSubmissions',
            'lowRiskSubmissions',
            'recentSubmissions',
            'topArticles'
        ));
    }
}
