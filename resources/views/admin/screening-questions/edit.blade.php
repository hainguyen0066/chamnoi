@extends('layouts.admin')

@section('title', 'Sửa Câu Hỏi Sàng Lọc - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Chỉnh Sửa Câu Hỏi Sàng Lọc</h2>
            <p class="text-xs text-slate-500">Cập nhật tiêu chí đánh giá #{{ $screeningQuestion->id }}</p>
        </div>
        <a href="{{ route('admin.screening-questions.index', ['age_group' => $screeningQuestion->age_group]) }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&larr; Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.screening-questions.update', $screeningQuestion->id) }}" class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nhóm tuổi <span class="text-rose-500">*</span></label>
                <select name="age_group" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    @foreach($ageGroups as $key => $name)
                        <option value="{{ $key }}" {{ old('age_group', $screeningQuestion->age_group) === $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phân loại kỹ năng</label>
                <select name="category" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    <option value="ngon_ngu" {{ $screeningQuestion->category === 'ngon_ngu' ? 'selected' : '' }}>Ngôn ngữ & Vốn từ</option>
                    <option value="giao_tiep_mat" {{ $screeningQuestion->category === 'giao_tiep_mat' ? 'selected' : '' }}>Giao tiếp mắt</option>
                    <option value="tuong_tac" {{ $screeningQuestion->category === 'tuong_tac' ? 'selected' : '' }}>Tương tác xã hội & Điệu bộ</option>
                    <option value="hanh_vi" {{ $screeningQuestion->category === 'hanh_vi' ? 'selected' : '' }}>Hành vi & Cảm giác</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nội dung câu hỏi <span class="text-rose-500">*</span></label>
            <textarea name="question" rows="2" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('question', $screeningQuestion->question) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Giải thích cho phụ huynh hiểu</label>
            <textarea name="explanation" rows="2" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('explanation', $screeningQuestion->explanation) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Thứ tự hiển thị (STT)</label>
                <input type="number" name="order_index" value="{{ old('order_index', $screeningQuestion->order_index) }}" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-center gap-4 pt-6">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-rose-700">
                    <input type="checkbox" name="is_red_flag" value="1" {{ $screeningQuestion->is_red_flag ? 'checked' : '' }} class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <span>🚩 Đánh dấu DẤU HIỆU CỜ ĐỎ</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" {{ $screeningQuestion->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Kích hoạt</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.screening-questions.index', ['age_group' => $screeningQuestion->age_group]) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700">Hủy</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection
