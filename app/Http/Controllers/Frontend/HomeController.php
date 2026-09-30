<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\View\View;

/**
 * Trang chủ frontend.
 */
class HomeController extends Controller
{
    /**
     * Trang chủ: khối tab tin tức (4 danh mục đầu, mỗi tab 4 bài mới nhất)
     * + slider "tính năng đặc sắc" quản lý từ admin (zone home_hero).
     */
    public function index(): View
    {
        $newsTabs = Category::query()
            ->where('status', 'active')
            ->orderBy('sort')
            ->limit(4)
            ->with(['posts' => function ($query) {
                $query->published()->latest('published_at')->limit(4);
            }])
            ->get();

        return view('frontend.home', [
            'newsTabs' => $newsTabs,
            'sliders' => Slider::forZone('home_hero')->get(),
        ]);
    }
}
