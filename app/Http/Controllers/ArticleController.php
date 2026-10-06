<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('is_published', true);

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($ageGroup = $request->get('age_group')) {
            $query->where(function ($q) use ($ageGroup) {
                $q->where('age_group', $ageGroup)
                  ->orWhere('age_group', 'all');
            });
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('order_index')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $categories = Article::getCategoryLabels();
        $selectedCategory = $request->get('category');
        $selectedAgeGroup = $request->get('age_group');

        return view('articles.index', compact(
            'articles',
            'categories',
            'selectedCategory',
            'selectedAgeGroup'
        ));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $article->increment('views_count');

        $relatedArticles = Article::where('is_published', true)
            ->where('id', '!=', $article->id)
            ->where(function ($q) use ($article) {
                $q->where('category', $article->category)
                  ->orWhere('is_featured', true);
            })
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    public function compare()
    {
        $compareArticle = Article::where('slug', 'phan-biet-cham-noi-don-thuan-va-tu-ky')->first();

        return view('articles.compare', compact('compareArticle'));
    }
}
