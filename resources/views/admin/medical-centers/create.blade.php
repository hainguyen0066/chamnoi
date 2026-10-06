@extends('layouts.admin')

@section('title', 'Thêm Cơ Sở Y Tế Mới - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Thêm Cơ Sở Y Tế Mới</h2>
            <p class="text-xs text-slate-500">Bổ sung bệnh viện hoặc trung tâm âm ngữ trị liệu.</p>
        </div>
        <a href="{{ route('admin.medical-centers.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&larr; Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.medical-centers.store') }}" class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tên Bệnh viện / Trung tâm <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tỉnh / Thành phố <span class="text-rose-500">*</span></label>
                <select name="city" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ old('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Địa chỉ cụ thể <span class="text-rose-500">*</span></label>
            <input type="text" name="address" value="{{ old('address') }}" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Số điện thoại liên hệ</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="(028) 3927 1119" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Trang web (nếu có)</label>
                <input type="url" name="website" value="{{ old('website') }}" placeholder="https://..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Chuyên khoa & Dịch vụ <span class="text-rose-500">*</span></label>
            <input type="text" name="specialty" value="{{ old('specialty') }}" required placeholder="Ví dụ: Đánh giá chậm nói, Tự kỷ, Âm ngữ trị liệu, Đo thính lực..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Hướng dẫn đặt khám cho phụ huynh</label>
            <textarea name="booking_tip" rows="2" placeholder="Ví dụ: Nên gọi đặt hẹn trước 1 tuần, đi buổi sáng..." class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('booking_tip') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Mô tả chi tiết</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                <input type="checkbox" name="is_verified" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>✓ Đánh dấu là Đơn vị chính thống uy tín</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.medical-centers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700">Hủy</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">Thêm bệnh viện</button>
        </div>
    </form>
</div>
@endsection
