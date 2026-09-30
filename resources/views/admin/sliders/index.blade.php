<x-admin-layout>
    <x-slot name="header">Slider</x-slot>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.sliders.index') }}" class="flex items-center gap-2">
            <select name="zone" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả vị trí</option>
                @foreach (\App\Models\Slider::ZONES as $value => $label)
                    <option value="{{ $value }}" @selected(request('zone') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded-md">Lọc</button>
        </form>

        <a href="{{ route('admin.sliders.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
            + Thêm slider
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3 w-28">Ảnh</th>
                    <th class="px-5 py-3">Tiêu đề</th>
                    <th class="px-5 py-3">Vị trí</th>
                    <th class="px-5 py-3">Liên kết</th>
                    <th class="px-5 py-3 text-right">Thứ tự</th>
                    <th class="px-5 py-3">Trạng thái</th>
                    <th class="px-5 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($sliders as $slider)
                    <tr>
                        <td class="px-5 py-3">
                           
							<img src="{{ str_starts_with($slider->image, 'http') ? $slider->image : Storage::disk('public')->url($slider->image) }}" alt=""
                                     class="w-14 h-10 object-cover rounded">	 
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $slider->title ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-indigo-100 text-indigo-700">
                                {{ \App\Models\Slider::ZONES[$slider->zone] ?? $slider->zone }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 max-w-[200px] truncate">{{ $slider->url ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">{{ $slider->sort }}</td>
                        <td class="px-5 py-3">
                            @if ($slider->status)
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Mở</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Đóng</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-indigo-600 hover:underline">Sửa</a>
                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="inline ml-3"
                                  onsubmit="return confirm('Xoá slider này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400">Chưa có slider nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sliders->links() }}</div>
</x-admin-layout>
