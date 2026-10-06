@extends('layouts.admin')

@section('title', 'Quản lý Lộ Trình 30 Ngày - Mầm Ngôn Ngữ')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Lộ Trình 30 Ngày "Cùng Con Bật Âm"</h2>
            <p class="text-sm text-slate-500">Giáo trình 30 bài tập tương tác can thiệp sớm tại nhà cho cha mẹ.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5 w-16 text-center">Ngày</th>
                        <th class="p-3.5">Tiêu đề & Trò chơi</th>
                        <th class="p-3.5">Mục tiêu</th>
                        <th class="p-3.5">Từ vựng mục tiêu</th>
                        <th class="p-3.5">Thời lượng</th>
                        <th class="p-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($days as $day)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5 text-center">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-black inline-flex items-center justify-center text-xs">
                                    {{ $day->day_number }}
                                </span>
                            </td>
                            <td class="p-3.5 max-w-sm">
                                <div class="font-bold text-slate-800">{{ $day->title }}</div>
                                <div class="text-emerald-700 text-[11px] font-semibold">🎲 {{ $day->activity_name }}</div>
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs truncate" title="{{ $day->goal }}">
                                {{ $day->goal }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 font-semibold text-[11px]">
                                    {{ $day->target_words ?? '—' }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono text-slate-600">{{ $day->duration_minutes }} phút</td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('admin.roadmap.edit', $day->id) }}" class="text-sky-600 hover:text-sky-700 font-bold">Chỉnh sửa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
