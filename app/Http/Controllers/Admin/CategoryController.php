<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CRUD danh mục bài viết.
 */
class CategoryController extends Controller
{
    /**
     * Danh sách danh mục kèm danh mục cha và số bài viết.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->withCount('posts')
            ->orderBy('sort')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Form tạo danh mục.
     */
    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(),
            'parents' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Lưu danh mục mới.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::query()->create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã tạo danh mục.');
    }

    /**
     * Form sửa danh mục.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            // Loại chính nó khỏi danh sách cha để không tự chọn mình.
            'parents' => Category::query()->whereKeyNot($category->id)->orderBy('name')->get(),
        ]);
    }

    /**
     * Cập nhật danh mục.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã cập nhật danh mục.');
    }

    /**
     * Xoá danh mục. Chặn xoá khi còn bài viết để tránh mất liên kết dữ liệu.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->posts()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Danh mục còn bài viết, hãy chuyển bài viết sang danh mục khác trước khi xoá.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã xoá danh mục.');
    }
}
