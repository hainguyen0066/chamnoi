@extends('layouts.admin')

@section('title', 'Quản lý Bộ Câu Hỏi Sàng Lọc - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Bộ Câu Hỏi Sàng Lọc</h2>
            <p class="text-sm text-slate-500">Cấu hình các tiêu chí kiểm tra mốc phát triển và dấu hiệu cờ đỏ theo tháng tuổi.</p>
        </div>
        <a href="{{ route('admin.screening-questions.create', ['age_group' => $selectedAge]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Thêm câu hỏi mới</span>
        </a>
    </div>

    <!-- Age Tabs -->
    <div class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200 flex flex-wrap gap-2">
        @foreach($ageGroups as $key => $name)
            <a href="{{ route('admin.screening-questions.index', ['age_group' => $key]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedAge === $key ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $name }}
            </a>
        @endforeach
    </div>

    <!-- Questions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5 w-12 text-center">STT</th>
                        <th class="p-3.5">Nội dung câu hỏi & Giải thích</th>
                        <th class="p-3.5">Phân loại</th>
                        <th class="p-3.5">Cờ đỏ (Nguy cơ)</th>
                        <th class="p-3.5">Trạng thái</th>
                        <th class="p-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $q)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5 text-center font-bold text-slate-400">{{ $q->order_index ?: $loop->iteration }}</td>
                            <td class="p-3.5 max-w-md">
                                <div class="font-bold text-slate-800">{{ $q->question }}</div>
                                @if($q->explanation)
                                    <div class="text-slate-400 text-[11px] mt-0.5">{{ $q->explanation }}</div>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-medium text-[10px]">{{ $q->category }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($q->is_red_flag)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">🚩 Cờ đỏ</span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Bình thường</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($q->is_active)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Kích hoạt</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">Tắt</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right space-x-2">
                                <a href="{{ route('admin.screening-questions.edit', $q->id) }}" class="text-sky-600 hover:text-sky-700 font-bold">Sửa</a>
                                <form method="POST" action="{{ route('admin.screening-questions.destroy', $q->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Chưa có câu hỏi nào cho nhóm tuổi này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
