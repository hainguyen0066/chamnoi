@extends('layouts.admin')

@section('title', 'Quản lý Phiếu Sàng Lọc Phụ Huynh - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Phiếu Sàng Lọc Của Phụ Huynh</h2>
            <p class="text-sm text-slate-500">Danh sách các bài test trắc nghiệm phụ huynh đã thực hiện trên website.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('admin.screening-submissions.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên bé, phụ huynh, SĐT..." class="flex-1 px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
            
            <select name="risk_level" class="px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả mức độ nguy cơ</option>
                <option value="high" {{ request('risk_level') === 'high' ? 'selected' : '' }}>Nguy cơ cao (Cần khám)</option>
                <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Cần theo dõi</option>
                <option value="low" {{ request('risk_level') === 'low' ? 'selected' : '' }}>Bình thường</option>
            </select>

            <select name="status" class="px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                <option value="">Tất cả trạng thái</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Mới nhận</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Đã xem xét</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Đã tư vấn</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-semibold text-xs rounded-xl hover:bg-slate-700">Lọc dữ liệu</button>
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">Mã phiếu / Bé</th>
                        <th class="p-3.5">Phụ huynh & SĐT</th>
                        <th class="p-3.5">Nhóm tuổi</th>
                        <th class="p-3.5">Điểm / Cờ đỏ</th>
                        <th class="p-3.5">Mức độ nguy cơ</th>
                        <th class="p-3.5">Trạng thái</th>
                        <th class="p-3.5">Ngày gửi</th>
                        <th class="p-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $sub)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800">#{{ $sub->id }} - {{ $sub->child_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $sub->child_age_months }} tháng tuổi</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-700">{{ $sub->parent_name ?? 'Ẩn danh' }}</div>
                                <div class="font-mono text-[11px] text-slate-500">{{ $sub->parent_phone ?? '—' }}</div>
                            </td>
                            <td class="p-3.5 font-medium text-slate-600">{{ $sub->age_group }}</td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-700">{{ $sub->score }}/{{ $sub->total_questions }}</span>
                                @if($sub->red_flags_count > 0)
                                    <span class="block text-[10px] text-rose-600 font-bold">🚩 {{ $sub->red_flags_count }} cờ đỏ</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($sub->risk_level === 'high')
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">Nguy cơ cao</span>
                                @elseif($sub->risk_level === 'medium')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">Cần theo dõi</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Bình thường</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($sub->status === 'new')
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold text-[10px]">Mới</span>
                                @elseif($sub->status === 'contacted')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Đã tư vấn</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">Đã xem</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-400 text-[11px]">{{ $sub->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3.5 text-right space-x-2">
                                <a href="{{ route('admin.screening-submissions.show', $sub->id) }}" class="text-emerald-600 hover:text-emerald-700 font-bold">Xem chi tiết</a>
                                <form method="POST" action="{{ route('admin.screening-submissions.destroy', $sub->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa phiếu này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Chưa có phiếu sàng lọc nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection
