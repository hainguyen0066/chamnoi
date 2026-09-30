<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\AnthropicService;
use App\Services\CdnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CRUD bài viết + upload ảnh cho editor + AI hỗ trợ viết bài (Claude).
 */
class PostController extends Controller
{
    /**
     * Danh sách bài viết, lọc theo từ khoá / danh mục / trạng thái.
     */
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->with(['categories', 'author'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->string('q') . '%');
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->integer('category_id')));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Form tạo bài viết.
     */
    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Lưu bài viết mới: upload thumbnail lên CDN, gán tác giả, sync danh mục.
     */
    public function store(StorePostRequest $request, CdnService $cdn): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            try {
                $data['thumbnail'] = $cdn->upload($request->file('thumbnail'));
            } catch (\RuntimeException $e) {
                return back()->withInput()->with('error', 'Upload thumbnail thất bại: ' . $e->getMessage());
            }
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = new Post($data);
        $post->author_id = auth('admin')->id();
        $post->save();

        $post->categories()->sync($data['category_ids']);

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Đã tạo bài viết.');
    }

    /**
     * Form sửa bài viết.
     */
    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post->load('categories'),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Cập nhật bài viết: upload thumbnail mới lên CDN nếu có, sync danh mục.
     */
    public function update(UpdatePostRequest $request, Post $post, CdnService $cdn): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            try {
                $data['thumbnail'] = $cdn->upload($request->file('thumbnail'));
                // Xoá ảnh cũ nếu là local storage (không xoá CDN cũ để tránh mất ảnh đang dùng)
                if ($post->thumbnail && !str_starts_with($post->thumbnail, 'http')) {
                    Storage::disk('public')->delete($post->thumbnail);
                }
            } catch (\RuntimeException $e) {
                return back()->withInput()->with('error', 'Upload thumbnail thất bại: ' . $e->getMessage());
            }
        } else {
            unset($data['thumbnail']);
        }

        if ($data['status'] === 'published' && empty($data['published_at']) && $post->published_at === null) {
            $data['published_at'] = now();
        }

        $post->update($data);
        $post->categories()->sync($data['category_ids']);

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Đã cập nhật bài viết.');
    }

    /**
     * Xoá bài viết kèm thumbnail.
     */
    public function destroy(Post $post): RedirectResponse
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete(); // pivot category_post tự xoá nhờ cascadeOnDelete

        return redirect()
            ->route('admin.posts.index')
            ->with('status', 'Đã xoá bài viết.');
    }

    /**
     * Endpoint upload ảnh chèn trong nội dung (TinyMCE gọi qua images_upload_url).
     * Ảnh được upload lên CDN, trả về {"location": "<cdn-url>"} theo định dạng TinyMCE.
     */
    public function uploadImage(Request $request, CdnService $cdn): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        try {
            $url = $cdn->upload($request->file('file'));
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['location' => $url]);
    }

    /**
     * AI hỗ trợ viết bài: nhận chủ đề, gọi Claude, trả HTML cho editor.
     * Route đã gắn throttle để giới hạn tần suất gọi API.
     */
    public function aiGenerate(Request $request, AnthropicService $anthropic): JsonResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
        ]);

        try {
            $content = $anthropic->generateContent($validated['topic']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['content' => $content]);
    }

    /**
     * Thư mục lưu thumbnail theo năm/tháng cho dễ quản lý.
     */
    private function thumbnailDir(): string
    {
        return 'posts/thumbnails/' . date('Y/m');
    }
}
