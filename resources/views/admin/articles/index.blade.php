@extends('layouts.admin')

@section('title', 'Quản lý Bài Viết & Cẩm Nang - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Bài Viết & Cẩm Nang</h2>
            <p class="text-sm text-slate-500">Quản lý nội dung hướng dẫn, nguyên nhân, bài tập can thiệp cho phụ huynh.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Viết bài mới</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm theo tiêu đề..." class="flex-1 px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            <select name="category" class="px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $key => $name)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-semibold text-xs rounded-xl hover:bg-slate-700">Lọc</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">Tiêu đề bài viết</th>
                        <th class="p-3.5">Danh mục</th>
                        <th class="p-3.5">Độ tuổi</th>
                        <th class="p-3.5">Trạng thái</th>
                        <th class="p-3.5">Lượt xem</th>
                        <th class="p-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 max-w-md">{{ $art->title }}</div>
                                <div class="text-slate-400 text-[11px] truncate max-w-md">{{ $art->excerpt }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[10px]">{{ $art->category_label }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600 font-medium">{{ $art->age_group }}</td>
                            <td class="p-3.5">
                                @if($art->is_published)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Hiển thị</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">Ẩn</span>
                                @endif
                                @if($art->is_featured)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-bold">Nổi bật</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-slate-600">{{ number_format($art->views_count) }}</td>
                            <td class="p-3.5 text-right space-x-2">
                                <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="text-slate-500 hover:text-emerald-600 font-bold">Xem</a>
                                <a href="{{ route('admin.articles.edit', $art->id) }}" class="text-sky-600 hover:text-sky-700 font-bold">Sửa</a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $art->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Không có bài viết nào phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $articles->links() }}
        </div>
    </div>
</div>
@endsection
