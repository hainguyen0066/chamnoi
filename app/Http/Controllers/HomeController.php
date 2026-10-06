<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ScreeningQuestion;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredArticles = Article::where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('order_index')
            ->take(6)
            ->get();

        $causesArticles = Article::where('is_published', true)
            ->where('category', 'nguyen-nhan')
            ->orderBy('order_index')
            ->take(3)
            ->get();

        $solutionArticles = Article::where('is_published', true)
            ->whereIn('category', ['cach-xu-ly', 'tro-choi'])
            ->orderBy('order_index')
            ->take(3)
            ->get();

        $recentArticles = Article::where('is_published', true)
            ->latest()
            ->take(4)
            ->get();

        $totalQuestions = ScreeningQuestion::where('is_active', true)->count();
        $hotline = SiteSetting::get('hotline', '1900 6868');
        $siteTagline = SiteSetting::get('site_tagline', 'Hiểu đúng nguyên nhân, đồng hành khoa học cùng con bật âm mỗi ngày');

        return view('home', compact(
            'featuredArticles',
            'causesArticles',
            'solutionArticles',
            'recentArticles',
            'totalQuestions',
            'hotline',
            'siteTagline'
        ));
    }
}
