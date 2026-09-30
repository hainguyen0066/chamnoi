<x-admin-layout>
    <x-slot name="header">Content Blocks</x-slot>

    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-gray-500">Quản lý các vùng nội dung HTML nhúng vào frontend.</p>
        <a href="{{ route('admin.blocks.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
            + Tạo block mới
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Tên định danh</th>
                    <th class="px-4 py-3">Label</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Cập nhật</th>
                    <th class="px-4 py-3">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($blocks as $block)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-indigo-700">{{ $block->name }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ $block->label }}</td>
                        <td class="px-4 py-3">
                            @if ($block->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Hoạt động</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Tắt</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $block->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 flex items-center gap-3">
                            <a href="{{ route('admin.blocks.edit', $block) }}"
                               class="text-indigo-600 hover:underline">Sửa</a>
                            <form method="POST" action="{{ route('admin.blocks.destroy', $block) }}"
                                  onsubmit="return confirm('Xoá block «{{ $block->label }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Chưa có block nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $blocks->links() }}</div>

    {{-- Hộp hướng dẫn nhúng --}}
    <div class="mt-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg text-sm text-indigo-900">
        <p class="font-semibold mb-1">Cách nhúng block vào frontend (volam-laravel):</p>
        <code class="block bg-white border border-indigo-100 rounded px-3 py-2 font-mono text-indigo-700 mt-1">
            &lt;x-block name="tên-định-danh" /&gt;
        </code>
        <p class="mt-2 text-indigo-700 text-xs">Ví dụ: <code>&lt;x-block name="homepage_banner" /&gt;</code> sẽ render nội dung của block có tên <code>homepage_banner</code>. Block bị tắt hoặc không tồn tại thì không hiện gì.</p>
    </div>
</x-admin-layout>
