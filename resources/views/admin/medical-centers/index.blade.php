@extends('layouts.admin')

@section('title', 'Quản lý Cơ Sở Y Tế - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Danh Bạ Cơ Sở Y Tế & Bệnh Viện</h2>
            <p class="text-sm text-slate-500">Quản lý các bệnh viện Nhi, khoa Tâm lý và phòng khám âm ngữ trị liệu.</p>
        </div>
        <a href="{{ route('admin.medical-centers.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Thêm bệnh viện mới</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">Tên cơ sở / Bệnh viện</th>
                        <th class="p-3.5">Tỉnh / Thành phố</th>
                        <th class="p-3.5">Chuyên khoa</th>
                        <th class="p-3.5">Điện thoại</th>
                        <th class="p-3.5">Xác thực</th>
                        <th class="p-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($centers as $c)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800">{{ $c->name }}</div>
                                <div class="text-slate-400 text-[11px] truncate max-w-xs">{{ $c->address }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px]">{{ $c->city }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs truncate" title="{{ $c->specialty }}">
                                {{ $c->specialty }}
                            </td>
                            <td class="p-3.5 font-mono text-slate-600">{{ $c->phone ?? '—' }}</td>
                            <td class="p-3.5">
                                @if($c->is_verified)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ Chính thống</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">Chưa duyệt</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right space-x-2">
                                <a href="{{ route('admin.medical-centers.edit', $c->id) }}" class="text-sky-600 hover:text-sky-700 font-bold">Sửa</a>
                                <form method="POST" action="{{ route('admin.medical-centers.destroy', $c->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa cơ sở này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Chưa có cơ sở y tế nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
