<x-admin-layout>
    <x-slot name="header">Bài viết</x-slot>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tiêu đề..."
                   class="rounded-md border-gray-300 shadow-sm text-sm">
            <select name="category_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="published" @selected(request('status') === 'published')>Đã đăng</option>
                <option value="draft" @selected(request('status') === 'draft')>Nháp</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded-md">Lọc</button>
        </form>

        <a href="{{ route('admin.posts.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
            + Viết bài mới
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3 w-20">Ảnh</th>
                    <th class="px-5 py-3">Tiêu đề</th>
                    <th class="px-5 py-3">Danh mục</th>
                    <th class="px-5 py-3">Tác giả</th>
                    <th class="px-5 py-3">Ngày đăng</th>
                    <th class="px-5 py-3">Trạng thái</th>
                    <th class="px-5 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-5 py-3">
                            @if ($post->thumbnail)
                                <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : Storage::disk('public')->url($post->thumbnail) }}" alt=""
                                     class="w-14 h-10 object-cover rounded">
                            @else
                                <div class="w-14 h-10 bg-gray-100 rounded"></div>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-medium text-gray-800">{{ $post->title }}</span>
                            @if ($post->is_featured)
                                <span class="ml-1 inline-block px-1.5 py-0.5 rounded text-xs bg-amber-100 text-amber-700">Nổi bật</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $post->categories->pluck('name')->join(', ') }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $post->author?->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if ($post->status === 'published')
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Đã đăng</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Nháp</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:underline">Sửa</a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline ml-3"
                                  onsubmit="return confirm('Xoá bài viết này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400">Chưa có bài viết nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</x-admin-layout>
