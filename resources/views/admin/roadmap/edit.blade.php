@extends('layouts.admin')

@section('title', 'Sửa Bài Tập Ngày ' . $interventionDay->day_number . ' - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Chỉnh Sửa Bài Tập Ngày {{ $interventionDay->day_number }}</h2>
            <p class="text-xs text-slate-500">Thuộc {{ \App\Models\InterventionDay::getWeeks()[$interventionDay->week_number] ?? '' }}</p>
        </div>
        <a href="{{ route('admin.roadmap.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&larr; Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.roadmap.update', $interventionDay->id) }}" class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tiêu đề ngày tập <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $interventionDay->title) }}" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tên trò chơi / Hoạt động <span class="text-rose-500">*</span></label>
                <input type="text" name="activity_name" value="{{ old('activity_name', $interventionDay->activity_name) }}" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Thời lượng (phút)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $interventionDay->duration_minutes) }}" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Mục tiêu bài tập <span class="text-rose-500">*</span></label>
            <input type="text" name="goal" value="{{ old('goal', $interventionDay->goal) }}" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Từ vựng / Âm thanh mục tiêu</label>
            <input type="text" name="target_words" value="{{ old('target_words', $interventionDay->target_words) }}" placeholder="Ví dụ: Ba, Mẹ, Gâu gâu..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Hướng dẫn chi tiết từng bước cho cha mẹ <span class="text-rose-500">*</span></label>
            <textarea name="instructions" rows="5" required class="w-full px-4 py-2.5 text-xs leading-relaxed rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('instructions', $interventionDay->instructions) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Lời khuyên của chuyên gia (Parent tip) <span class="text-rose-500">*</span></label>
            <textarea name="parent_tip" rows="3" required class="w-full px-4 py-2.5 text-xs leading-relaxed rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('parent_tip', $interventionDay->parent_tip) }}</textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.roadmap.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700">Hủy</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection
