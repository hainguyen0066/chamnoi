<x-admin-layout>
    <x-slot name="header">{{ $category->exists ? 'Sửa danh mục: ' . $category->name : 'Thêm danh mục' }}</x-slot>

    <form method="POST"
          action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
          class="bg-white shadow-sm rounded-lg p-6 max-w-2xl">
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (bỏ trống sẽ tự sinh)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục cha</label>
                <select id="parent_id" name="parent_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">— Không có —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <input type="text" id="description" name="description" value="{{ old('description', $category->description) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta title (SEO)</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta description (SEO)</label>
                <input type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', $category->meta_description) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            </div>

            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
                <input type="number" id="sort" name="sort" value="{{ old('sort', $category->sort ?? 0) }}" min="0"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            </div>

            <div>
                <label for="show_on_homepage" class="block text-sm font-medium text-gray-700 mb-1">Hiện ở danh mục trang chủ</label>
                <select id="show_on_homepage" name="show_on_homepage" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="0" @selected(!old('show_on_homepage', $category->show_on_homepage))>Không</option>
                    <option value="1" @selected((bool) old('show_on_homepage', $category->show_on_homepage))>Có</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="active" @selected(old('status', $category->status ?? 'active') === 'active')>Hoạt động</option>
                    <option value="inactive" @selected(old('status', $category->status) === 'inactive')>Ẩn</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                {{ $category->exists ? 'Cập nhật' : 'Tạo danh mục' }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md">
                Huỷ
            </a>
        </div>
    </form>
</x-admin-layout>
