<x-admin-layout>
    <x-slot name="header">{{ $slider->exists ? 'Sửa slider' : 'Thêm slider' }}</x-slot>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $slider->exists ? route('admin.sliders.update', $slider) : route('admin.sliders.store') }}"
          class="bg-white shadow-sm rounded-lg p-6 max-w-2xl">
        @csrf
        @if ($slider->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="zone" class="block text-sm font-medium text-gray-700 mb-1">Vị trí hiển thị *</label>
                <select id="zone" name="zone" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    @foreach (\App\Models\Slider::ZONES as $value => $label)
                        <option value="{{ $value }}" @selected(old('zone', $slider->zone) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('zone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                <input type="text" id="title" name="title" value="{{ old('title', $slider->title) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                    Ảnh chính {{ $slider->exists ? '(bỏ trống giữ ảnh cũ)' : '*' }}
                </label>
                @if ($slider->image)
                    <img src="{{ Storage::disk('public')->url($slider->image) }}" alt="" class="w-full rounded-md mb-2">
                @endif
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm text-gray-600">
                @error('image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="mobile_image" class="block text-sm font-medium text-gray-700 mb-1">Ảnh mobile (tuỳ chọn)</label>
                @if ($slider->mobile_image)
                    <img src="{{ Storage::disk('public')->url($slider->mobile_image) }}" alt="" class="w-full rounded-md mb-2">
                @endif
                <input type="file" id="mobile_image" name="mobile_image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm text-gray-600">
                @error('mobile_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">Liên kết khi bấm vào</label>
                <input type="url" id="url" name="url" value="{{ old('url', $slider->url) }}" placeholder="https://..."
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @error('url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <input type="text" id="description" name="description" value="{{ old('description', $slider->description) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
                <input type="number" id="sort" name="sort" value="{{ old('sort', $slider->sort ?? 0) }}" min="0"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            </div>

            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" @checked(old('status', $slider->status ?? true))
                           class="rounded border-gray-300">
                    Hiển thị (mở)
                </label>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                {{ $slider->exists ? 'Cập nhật' : 'Tạo slider' }}
            </button>
            <a href="{{ route('admin.sliders.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md">
                Huỷ
            </a>
        </div>
    </form>
</x-admin-layout>
