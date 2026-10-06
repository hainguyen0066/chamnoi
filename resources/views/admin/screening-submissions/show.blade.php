@extends('layouts.admin')

@section('title', 'Chi Tiết Phiếu Sàng Lọc #' . $screeningSubmission->id . ' - Mầm Ngôn Ngữ')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Phiếu Sàng Lọc #{{ $screeningSubmission->id }}</h2>
            <p class="text-xs text-slate-500">Gửi lúc {{ $screeningSubmission->created_at->format('d/m/Y H:i:s') }}</p>
        </div>
        <a href="{{ route('admin.screening-submissions.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">&larr; Quay lại danh sách</a>
    </div>

    <!-- Overview info box -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <span class="text-xs text-slate-400 font-medium">Bé</span>
            <div class="text-base font-bold text-slate-800">{{ $screeningSubmission->child_name }}</div>
            <div class="text-xs text-slate-500">{{ $screeningSubmission->child_age_months }} tháng tuổi ({{ $screeningSubmission->age_group }})</div>
        </div>

        <div>
            <span class="text-xs text-slate-400 font-medium">Phụ huynh</span>
            <div class="text-base font-bold text-slate-800">{{ $screeningSubmission->parent_name ?? 'Ẩn danh' }}</div>
            <div class="text-xs font-mono text-emerald-600 font-bold">{{ $screeningSubmission->parent_phone ?? 'Không để lại SĐT' }}</div>
        </div>

        <div>
            <span class="text-xs text-slate-400 font-medium">Mức độ đánh giá</span>
            <div class="mt-1">
                @if($screeningSubmission->risk_level === 'high')
                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold text-xs">🚩 Nguy cơ cao</span>
                @elseif($screeningSubmission->risk_level === 'medium')
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs">⚠️ Cần theo dõi</span>
                @else
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">✓ Bình thường</span>
                @endif
            </div>
            <div class="text-xs text-slate-400 mt-1">{{ $screeningSubmission->score }}/{{ $screeningSubmission->total_questions }} rủi ro ({{ $screeningSubmission->red_flags_count }} cờ đỏ)</div>
        </div>

        <div>
            <span class="text-xs text-slate-400 font-medium">Trạng thái</span>
            <div class="text-sm font-bold text-slate-800 mt-1 capitalize">{{ $screeningSubmission->status }}</div>
        </div>
    </div>

    @if($screeningSubmission->clinical_impression || $screeningSubmission->doctor_recommendation)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-3">
            <h3 class="font-bold text-sm text-slate-800">Kết luận phân tích hành vi & Định hướng y khoa</h3>
            @if($screeningSubmission->clinical_impression)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-slate-700">Chỉ báo xu hướng:</strong> {{ $screeningSubmission->clinical_impression }}
                </div>
            @endif
            @if($screeningSubmission->need_doctor)
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                    <span>🚨</span>
                    <span>Hệ thống chỉ định: CẦN ĐI KHÁM BÁC SĨ CHUYÊN KHOA GẤP!</span>
                </div>
            @endif
            @if($screeningSubmission->doctor_recommendation)
                <div class="text-xs text-slate-600 leading-relaxed">
                    <strong class="text-slate-700">Lời khuyên khám:</strong> {{ $screeningSubmission->doctor_recommendation }}
                </div>
            @endif
        </div>
    @endif

    <!-- Answers Breakdown -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-sm text-slate-800 mb-4">Chi tiết các câu trả lời của phụ huynh</h3>

        @if(is_array($screeningSubmission->answers))
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($screeningSubmission->answers as $ans)
                    <div class="py-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="font-medium text-slate-800 text-sm">{{ $ans['question'] ?? $ans['title'] ?? 'Câu hỏi' }}</p>
                            @if(!empty($ans['is_red_flag']))
                                <span class="text-[10px] text-rose-600 font-bold">🚩 Tiêu chí cờ đỏ</span>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2.5 py-1 rounded-lg font-bold text-[11px] {{ $ans['is_risk'] ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $ans['is_risk'] ? 'Chưa làm được / Rủi ro' : 'Bé làm tốt' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400">Không có dữ liệu chi tiết.</p>
        @endif
    </div>

    <!-- Update Status & Specialist Clinical Notes -->
    <form method="POST" action="{{ route('admin.screening-submissions.update', $screeningSubmission->id) }}" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
        @csrf
        @method('PATCH')

        <h3 class="font-bold text-sm text-slate-800">Cập nhật xử lý & Ghi chú tư vấn</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Trạng thái xử lý</label>
                <select name="status" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    <option value="new" {{ $screeningSubmission->status === 'new' ? 'selected' : '' }}>Mới nhận</option>
                    <option value="reviewed" {{ $screeningSubmission->status === 'reviewed' ? 'selected' : '' }}>Đã xem xét</option>
                    <option value="contacted" {{ $screeningSubmission->status === 'contacted' ? 'selected' : '' }}>Đã gọi điện tư vấn cho phụ huynh</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Ghi chú nội bộ chuyên viên (Notes)</label>
            <textarea name="admin_notes" rows="3" placeholder="Ghi chú về cuộc gọi, hẹn khám, lời khuyên đã trao đổi với phụ huynh..." class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">{{ old('admin_notes', $screeningSubmission->admin_notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm">
                Lưu cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
