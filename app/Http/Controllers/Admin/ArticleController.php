<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('order_index')->latest()->paginate(15)->withQueryString();
        $categories = Article::getCategoryLabels();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = Article::getCategoryLabels();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'category' => 'required|string',
            'age_group' => 'required|string',
            'reading_time' => 'required|string',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'required|string',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'order_index' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['order_index'] = $validated['order_index'] ?? 0;

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Đã tạo bài viết mới thành công!');
    }

    public function edit(Article $article)
    {
        $categories = Article::getCategoryLabels();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug,' . $article->id,
            'category' => 'required|string',
            'age_group' => 'required|string',
            'reading_time' => 'required|string',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'required|string',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'order_index' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['order_index'] = $validated['order_index'] ?? 0;

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Đã cập nhật bài viết thành công!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Đã xóa bài viết thành công!');
    }
}
