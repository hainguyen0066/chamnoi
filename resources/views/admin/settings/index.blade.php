@extends('layouts.admin')

@section('title', 'Cấu Hình Hệ Thống - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Cấu Hình Website</h2>
            <p class="text-xs text-slate-500">Thiết lập các thông tin liên hệ, hotline, lời khuyên y khoa hiển thị trên website.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf

        @foreach($settings as $group => $items)
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                <h3 class="font-bold text-sm text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Nhóm cấu hình: {{ strtoupper($group) }}
                </h3>

                <div class="space-y-4">
                    @foreach($items as $s)
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                {{ $s->label }}
                                <span class="text-slate-400 font-mono text-[10px]">({{ $s->key }})</span>
                            </label>

                            @if(str_contains($s->key, 'disclaimer') || strlen($s->value) > 100)
                                <textarea name="{{ $s->key }}" rows="3" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old($s->key, $s->value) }}</textarea>
                            @else
                                <input type="text" name="{{ $s->key }}" value="{{ old($s->key, $s->value) }}" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md">
                Lưu toàn bộ cấu hình
            </button>
        </div>
    </form>
</div>
@endsection
