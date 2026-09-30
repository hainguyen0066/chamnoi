<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Khu tin tức frontend: danh sách (lọc danh mục + tìm kiếm) và chi tiết bài.
 */
class NewsController extends Controller
{
    /**
     * Danh sách tin tức. Query string:
     *   danh-muc : slug danh mục cần lọc
     *   q        : từ khoá tìm theo tiêu đề
     */
    public function index(Request $request): View
    {
        $currentCategory = $request->query('danh-muc');

        $posts = Post::query()
            ->published()
            ->when($currentCategory, function ($query) use ($currentCategory) {
                $query->whereHas('categories', fn ($q) => $q->where('slug', $currentCategory));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->string('q') . '%');
            })
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('frontend.news.index', [
            'posts' => $posts,
            'categories' => $this->navCategories(),
            'currentCategory' => $currentCategory,
        ]);
    }

    /**
     * Chi tiết bài viết theo slug. Chỉ hiện bài đã đăng — bài nháp trả 404.
     */
    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->with('categories')
            ->firstOrFail();

        return view('frontend.news.show', [
            'post' => $post,
            'categories' => $this->navCategories(),
            'currentCategory' => $post->categories->first()?->slug,
        ]);
    }

    /**
     * Danh mục hiển thị trên thanh tab của khu tin tức.
     */
    private function navCategories()
    {
        return Category::query()
            ->where('status', 'active')
            ->orderBy('sort')
            ->limit(4)
            ->get();
    }
}
