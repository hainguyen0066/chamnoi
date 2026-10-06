@extends('layouts.admin')

@section('title', 'Thêm Câu Hỏi Sàng Lọc - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Thêm Câu Hỏi Sàng Lọc</h2>
            <p class="text-xs text-slate-500">Bổ sung tiêu chí đánh giá mốc ngôn ngữ hoặc hành vi.</p>
        </div>
        <a href="{{ route('admin.screening-questions.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&larr; Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.screening-questions.store') }}" class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nhóm tuổi <span class="text-rose-500">*</span></label>
                <select name="age_group" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    @foreach($ageGroups as $key => $name)
                        <option value="{{ $key }}" {{ old('age_group', $defaultAge) === $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phân loại kỹ năng</label>
                <select name="category" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    <option value="ngon_ngu">Ngôn ngữ & Vốn từ</option>
                    <option value="giao_tiep_mat">Giao tiếp mắt</option>
                    <option value="tuong_tac">Tương tác xã hội & Điệu bộ</option>
                    <option value="hanh_vi">Hành vi & Cảm giác</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nội dung câu hỏi <span class="text-rose-500">*</span></label>
            <textarea name="question" rows="2" required placeholder="Ví dụ: Bé có biết chỉ tay vào đồ vật bé muốn không?" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('question') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Giải thích cho phụ huynh hiểu</label>
            <textarea name="explanation" rows="2" placeholder="Ví dụ: Chỉ tay là cột mốc giao tiếp không lời quan trọng trước khi trẻ biết nói..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('explanation') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Thứ tự hiển thị (STT)</label>
                <input type="number" name="order_index" value="{{ old('order_index', 1) }}" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-center gap-4 pt-6">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-rose-700">
                    <input type="checkbox" name="is_red_flag" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <span>🚩 Đánh dấu DẤU HIỆU CỜ ĐỎ</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Kích hoạt</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.screening-questions.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700">Hủy</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">Thêm câu hỏi</button>
        </div>
    </form>
</div>
@endsection
