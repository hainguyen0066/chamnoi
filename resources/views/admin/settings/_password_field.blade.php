<div class="relative">
    <input type="password" id="{{ $id }}" name="{{ $name }}"
           value="{{ $value }}"
           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm pr-10">
    <button type="button" onclick="togglePassword('{{ $id }}')"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600"
            title="Ẩn/hiện">
        @include('admin.settings._eye_icon')
    </button>
</div>
