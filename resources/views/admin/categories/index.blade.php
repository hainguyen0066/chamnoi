<x-admin-layout>
    <x-slot name="header">Danh mục bài viết</x-slot>

    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.categories.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
            + Thêm danh mục
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3">Tên danh mục</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Danh mục cha</th>
                    <th class="px-5 py-3 text-right">Bài viết</th>
                    <th class="px-5 py-3 text-right">Thứ tự</th>
                    <th class="px-5 py-3">Trạng thái</th>
                    <th class="px-5 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $category->slug }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $category->parent?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">{{ $category->posts_count }}</td>
                        <td class="px-5 py-3 text-right">{{ $category->sort }}</td>
                        <td class="px-5 py-3">
                            @if ($category->status === 'active')
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Hoạt động</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Ẩn</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:underline">Sửa</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline ml-3"
                                  onsubmit="return confirm('Xoá danh mục {{ $category->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400">Chưa có danh mục nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-admin-layout>
