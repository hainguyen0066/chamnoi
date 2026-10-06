@extends('layouts.admin')

@section('title', 'Thêm Bài Viết Mới - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Thêm Bài Viết Mới</h2>
            <p class="text-xs text-slate-500">Soạn nội dung hướng dẫn hoặc bài tập can thiệp ngôn ngữ.</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&larr; Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.articles.store') }}" class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tiêu đề bài viết <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Danh mục <span class="text-rose-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    @foreach($categories as $key => $name)
                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Độ tuổi phù hợp</label>
                <select name="age_group" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    <option value="all">Tất cả lứa tuổi</option>
                    <option value="0-12m">0 – 12 tháng</option>
                    <option value="12-24m">12 – 24 tháng</option>
                    <option value="2-3y">2 – 3 tuổi</option>
                    <option value="3-5y">3 – 5 tuổi</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Thời gian đọc</label>
                <input type="text" name="reading_time" value="{{ old('reading_time', '5 phút đọc') }}" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Huy hiệu (Badge text)</label>
                <input type="text" name="badge_text" value="{{ old('badge_text', 'Thực hành ngay') }}" placeholder="Ví dụ: Quan trọng, Nên đọc..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Màu huy hiệu</label>
                <select name="badge_color" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    <option value="emerald">Xanh lá (Emerald)</option>
                    <option value="blue">Xanh dương (Blue)</option>
                    <option value="rose">Đỏ hồng (Rose)</option>
                    <option value="amber">Vàng cam (Amber)</option>
                    <option value="purple">Tím (Purple)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Đoạn tóm tắt (Excerpt) <span class="text-rose-500">*</span></label>
            <textarea name="excerpt" rows="2" required placeholder="Tóm tắt ngắn gọn 1-2 câu cho phụ huynh dễ hiểu..." class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('excerpt') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nội dung chi tiết (Hỗ trợ HTML) <span class="text-rose-500">*</span></label>
            <textarea name="content" rows="12" required placeholder="<h3>Tiêu đề đoạn</h3><p>Nội dung...</p>" class="w-full px-4 py-3 text-sm font-mono rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('content') }}</textarea>
        </div>

        <!-- SEO Meta Settings (Chuẩn Google Tìm Kiếm) -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
            <div class="flex items-center gap-2">
                <span class="text-base">🚀</span>
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-800">Tối ưu hóa SEO Google (Tăng Traffic)</h4>
            </div>
            <p class="text-xs text-slate-500">Nếu để trống, hệ thống sẽ tự động dùng Tiêu đề và Tóm tắt làm thẻ meta SEO.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">SEO Title (Tiêu đề Google)</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" placeholder="Tiêu đề chuẩn SEO (dưới 65 ký tự)" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Từ khóa SEO (Keywords)</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="trẻ chậm nói, dấu hiệu tự kỷ, can thiệp tại nhà..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">SEO Description (Mô tả Google Snippet)</label>
                <textarea name="meta_description" rows="2" placeholder="Mô tả hấp dẫn kích thích phụ huynh click vào từ Google (khoảng 150 - 160 ký tự)..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('meta_description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Link Ảnh Chia Sẻ Mạng Xã Hội (og:image)</label>
                <input type="url" name="og_image" value="{{ old('og_image') }}" placeholder="https://example.com/banner-bai-viet.jpg" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>Hiển thị trên website</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>Ghim bài nổi bật ngoài Trang chủ</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700">Hủy</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">Đăng bài viết</button>
        </div>
    </form>
</div>
@endsection
