<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Kick Người Chơi</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-mono font-semibold">GameServer API v2</span>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="gameKickApp()">
        {{-- Top Summary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tổng lệnh kick</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</span>
                    <span class="text-xs text-slate-400">lịch sử</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-emerald-50/40">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Đã kick thành công</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-emerald-700">{{ number_format($stats['success']) }}</span>
                    <span class="inline-flex items-center text-xs font-medium text-emerald-600">
                        <svg class="w-3.5 h-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        OK
                    </span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-amber-50/40">
                <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Đang chờ xử lý</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-amber-700">{{ number_format($stats['pending']) }}</span>
                    <span class="text-xs text-amber-600">Pending</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-rose-100 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-rose-50/40">
                <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Không online / Lỗi</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-rose-700">{{ number_format($stats['failed']) }}</span>
                    <span class="text-xs text-rose-500">Offline</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-indigo-50/40 col-span-2 sm:col-span-1">
                <span class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Lệnh hôm nay</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-indigo-700">{{ number_format($stats['today']) }}</span>
                    <span class="text-xs text-indigo-500">Hôm nay</span>
                </div>
            </div>
        </div>

        {{-- Main Kick Form (Full width, ẩn cơ chế và thông tin kết nối) --}}
        <div id="target-form-card" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-mt-6">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Thực Hiện Kick Người Chơi</h2>
                        <p class="text-xs text-slate-500">Tìm kiếm và gỡ người chơi khỏi toàn bộ 8 cụm GameServer ngay lập tức</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-slate-200/70 text-slate-600 px-2.5 py-1 rounded-md font-mono">Max 30 req/min</span>
                </div>
            </div>

            <form @submit.prevent="submitKick" class="p-6 space-y-5">
                {{-- Type Selector --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                        Phương thức kick <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label :class="form.type === 'account' ? 'border-rose-500 bg-rose-50/60 ring-2 ring-rose-500/20 text-rose-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                               class="relative flex items-center p-3.5 border rounded-xl cursor-pointer transition">
                            <input type="radio" value="account" x-model="form.type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                     :class="form.type === 'account' ? 'border-rose-600 bg-rose-600' : 'border-slate-300'">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="form.type === 'account'"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">Theo Tên Tài Khoản</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Khuyên dùng (chính xác tuyệt đối, không dấu)</div>
                                </div>
                            </div>
                        </label>

                        <label :class="form.type === 'role' ? 'border-rose-500 bg-rose-50/60 ring-2 ring-rose-500/20 text-rose-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                               class="relative flex items-center p-3.5 border rounded-xl cursor-pointer transition">
                            <input type="radio" value="role" x-model="form.type" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                     :class="form.type === 'role' ? 'border-rose-600 bg-rose-600' : 'border-slate-300'">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="form.type === 'role'"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">Theo Tên Nhân Vật</div>
                                    <div class="text-xs text-slate-500 mt-0.5">UTF-8 có dấu (phân biệt hoa/thường)</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Target Name & Reason in 2 cols on md+ --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Target Name --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <span x-text="form.type === 'account' ? 'Tên Tài Khoản Game' : 'Tên Nhân Vật Trong Game'"></span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Phân cách bằng dấu phẩy <code>,</code></span>
                        </div>
                        <div class="relative">
                            <input type="text"
                                   id="input-target-name"
                                   x-model="form.name"
                                   required
                                   :placeholder="form.type === 'account' ? 'Vd: acc01, acc02, acc03 (kích nhiều acc)' : 'Vd: Long Ngũ, Tiêu Phong, Dương Quá...'"
                                   :class="isReloading ? 'ring-4 ring-rose-400 bg-rose-50 border-rose-400' : 'bg-slate-50 border-slate-300 focus:bg-white'"
                                   class="w-full px-4 py-2.5 border rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 font-medium transition-all duration-300 text-sm">
                            <button type="button"
                                    x-show="form.name"
                                    @click="form.name = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs">
                                ✕
                            </button>
                        </div>

                        {{-- Preview Chips khi phát hiện nhiều tài khoản --}}
                        <template x-if="isBatch">
                            <div class="mt-2.5 p-3 rounded-xl bg-slate-50 border border-rose-200/80">
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-700 mb-1.5">
                                    <span class="flex items-center gap-1.5 text-rose-600">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                        </svg>
                                        Đã nhận diện <span x-text="parsedNames.length"></span> đối tượng
                                    </span>
                                    <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-medium border border-emerald-200/60">
                                        Chạy trực tiếp trên Hosting (giãn cách 2.0s)
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                                    <template x-for="(nameItem, idx) in parsedNames" :key="idx">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-white border border-slate-200 text-slate-800 shadow-2xs">
                                            <span class="text-rose-500 font-bold" x-text="'#' + (idx + 1)"></span>
                                            <span x-text="nameItem"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <p class="text-xs text-slate-500 mt-1.5" x-show="form.type === 'role' && !isBatch">
                            ⚠️ <strong>Lưu ý:</strong> Phân biệt hoa/thường và 7 chữ: <code>Ă Â Ê Ô Ơ Ư Đ</code>.
                        </p>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                Lý do kick <span class="text-slate-400 font-normal">(tùy chọn)</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Tối đa 128 byte</span>
                        </div>
                        <input type="text"
                               x-model="form.reason"
                               placeholder="Ví dụ: spam kênh thế giới, kẹt acc cần giải phóng..."
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 text-sm transition">
                    </div>
                </div>

                {{-- Prominent Loading State Banner during Kick --}}
                <div x-show="loading"
                     x-transition
                     class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 text-amber-900 flex items-center gap-3.5 shadow-xs"
                     style="display: none;">
                    <div class="relative flex items-center justify-center">
                        <svg class="animate-spin w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                    <div class="text-xs">
                        <div class="font-bold flex items-center gap-1.5 text-amber-800">
                            <span>Đang kết nối API và kiểm tra trên 8 cụm GameServer...</span>
                        </div>
                        <p class="text-amber-700/90 mt-0.5">Tiến trình có thể mất khoảng 3 - 6 giây để server tìm kiếm nhân vật. Vui lòng không đóng tab.</p>
                    </div>
                </div>

                {{-- Action Button & Info --}}
                <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-t border-slate-100">
                    <div class="text-xs text-slate-500">
                        Admin thao tác: <span class="font-semibold text-slate-700">{{ auth('admin')->user()->name ?? 'Admin' }}</span>
                    </div>
                    <button type="submit"
                            :disabled="loading || !form.name"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-rose-600/25 transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <template x-if="loading">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                            </svg>
                        </template>
                        <span x-text="loading ? 'Đang gửi lệnh...' : (isBatch ? 'Đưa ' + parsedNames.length + ' Tài Khoản Vào Hàng Đợi (Queue VPS)' : 'Xác Nhận Kick Ngay')"></span>
                    </button>
                </div>

                {{-- Live Alert Box for Latest Kick Result --}}
                <div x-show="lastResult"
                     x-transition
                     :class="{
                         'bg-emerald-50 border-emerald-200 text-emerald-900': lastResult?.code === '1',
                         'bg-amber-50 border-amber-200 text-amber-900': lastResult?.code === '0',
                         'bg-rose-50 border-rose-200 text-rose-900': lastResult?.code === '2'
                     }"
                     class="p-4 rounded-xl border mt-3 space-y-2 text-sm"
                     style="display: none;">
                    <div class="flex items-center justify-between font-bold">
                        <div class="flex items-center gap-2">
                            <span class="text-base" x-text="lastResult?.code === '1' ? '✅' : (lastResult?.code === '0' ? '⏳' : '❌')"></span>
                            <span x-text="lastResult?.msg || 'Kết quả phản hồi'"></span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded font-mono"
                              :class="lastResult?.code === '1' ? 'bg-emerald-200/80 text-emerald-800' : (lastResult?.code === '0' ? 'bg-amber-200/80 text-amber-800' : 'bg-rose-200/80 text-rose-800')"
                              x-text="'code: ' + lastResult?.code + ' (' + lastResult?.result + ')'"></span>
                    </div>
                    <template x-if="lastResult?.code === '1'">
                        <div class="text-xs text-emerald-700 grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1 border-t border-emerald-200/60">
                            <div>GameServer: <span class="font-bold" x-text="lastResult?.log?.gs_id ? 'GS #' + lastResult?.log?.gs_id : 'Tất cả GS'"></span></div>
                            <div>Nhân vật: <span class="font-bold" x-text="lastResult?.log?.role_name || '-'"></span></div>
                            <div>Ghi chú: <span class="font-bold" x-text="lastResult?.log?.note || 'Bình thường'"></span></div>
                        </div>
                    </template>
                </div>
            </form>
        </div>

        {{-- Live Queue In-Progress Banner --}}
        @if ($stats['pending'] > 0)
            <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center">
                        <svg class="animate-spin w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-amber-950 flex items-center gap-2">
                            <span>Đang có {{ $stats['pending'] }} lệnh trong Hàng Đợi (Queue)</span>
                            <span class="text-[11px] font-mono text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md font-medium">
                                Xử lý ngầm (1 lần/acc)
                            </span>
                        </div>
                        <div class="text-xs text-amber-800 mt-0.5">
                            Hệ thống đang kick từng tài khoản (giãn cách 2s giữa các tài khoản khác nhau để tránh rate limit). Không retry nếu thất bại.
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <button type="button"
                            @click="cancelAllQueue()"
                            :disabled="cancellingAll"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-lg shadow-sm transition disabled:opacity-60 cursor-pointer">
                        <template x-if="cancellingAll">
                            <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                        <template x-if="!cancellingAll">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </template>
                        <span x-text="cancellingAll ? 'Đang hủy...' : 'Hủy Toàn Bộ Hàng Đợi'"></span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Log & History Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Lịch Sử Các Lượt Kick</h3>
                    <p class="text-xs text-slate-500">Toàn bộ danh sách lệnh kick đã thực hiện cùng kết quả phản hồi từ GameServer</p>
                </div>

                {{-- Filter & Search Form --}}
                <form method="GET" action="{{ route('admin.game-kicks.index') }}" class="flex flex-wrap items-center gap-2">
                    <select name="type" class="text-xs rounded-lg border-slate-200 bg-slate-50 py-1.5 pl-2.5 pr-7 focus:ring-rose-500">
                        <option value="">-- Tất cả loại --</option>
                        <option value="account" @selected(request('type') === 'account')>Tài khoản</option>
                        <option value="role" @selected(request('type') === 'role')>Tên nhân vật</option>
                    </select>

                    <select name="code" class="text-xs rounded-lg border-slate-200 bg-slate-50 py-1.5 pl-2.5 pr-7 focus:ring-rose-500">
                        <option value="">-- Trạng thái --</option>
                        <option value="1" @selected(request('code') === '1')>Đã kick (Thành công)</option>
                        <option value="0" @selected(request('code') === '0')>Đang chờ (Pending)</option>
                        <option value="2" @selected(request('code') === '2')>Không kick được (Lỗi)</option>
                    </select>

                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Tìm tên, admin, lý do..."
                           class="text-xs rounded-lg border-slate-200 bg-slate-50 py-1.5 px-3 focus:ring-rose-500 w-44">

                    <button type="submit" class="text-xs px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-medium rounded-lg transition">
                        Lọc
                    </button>
                    @if (request()->hasAny(['type', 'code', 'q', 'from', 'to']))
                        <a href="{{ route('admin.game-kicks.index') }}" class="text-xs text-slate-500 hover:text-slate-800 py-1.5 px-2">
                            Xóa lọc
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80">
                            <th class="py-3 px-4">Thời gian</th>
                            <th class="py-3 px-4">Phương thức</th>
                            <th class="py-3 px-4">Mục tiêu (Tên)</th>
                            <th class="py-3 px-4">GameServer</th>
                            <th class="py-3 px-4">Trạng thái</th>
                            <th class="py-3 px-4">Phản hồi</th>
                            <th class="py-3 px-4">Người kick</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-mono">
                                    {{ $log->created_at->format('H:i:s d/m/Y') }}
                                    <div class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $log->type === 'account' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                        {{ $log->type_text }}
                                    </span>
                                </td>

                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $log->name }}</div>
                                    @if ($log->role_name && $log->role_name !== $log->name)
                                        <div class="text-[11px] text-slate-500">NV: <span class="font-medium text-slate-700">{{ $log->role_name }}</span></div>
                                    @endif
                                    @if ($log->target && $log->target !== $log->name)
                                        <div class="text-[11px] text-slate-500">TK: <span class="font-mono text-slate-700">{{ $log->target }}</span></div>
                                    @endif
                                    @if ($log->reason)
                                        <div class="text-[11px] text-slate-400 italic">Lý do: {{ $log->reason }}</div>
                                    @endif
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($log->gs_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            GS #{{ $log->gs_id }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $log->status_badge }}">
                                        {{ $log->status_label }}
                                    </span>
                                    @if ($log->result)
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $log->result }}</div>
                                    @endif
                                </td>

                                <td class="py-3 px-4 max-w-xs">
                                    <div class="font-medium text-slate-800 truncate" title="{{ $log->msg }}">{{ $log->msg ?: '-' }}</div>
                                    @if ($log->note)
                                        <div class="text-[11px] text-amber-700 bg-amber-50 rounded px-1.5 py-0.5 mt-0.5 inline-block font-mono">
                                            {{ $log->note }}
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                    <div class="font-medium text-slate-800">{{ $log->by }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">ID API: {{ $log->api_id ?? 'N/A' }}</div>
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap text-right space-x-1">
                                    @if ($log->code === '0')
                                        {{-- Nút Hủy lệnh đang chờ --}}
                                        <button type="button"
                                                @click="cancelSingle({{ $log->id }}, '{{ route('admin.game-kicks.cancel', $log) }}')"
                                                :disabled="cancellingIds.includes({{ $log->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-medium rounded-md shadow-xs transition disabled:opacity-60 cursor-pointer"
                                                title="Hủy lệnh này khỏi hàng đợi">
                                            <template x-if="cancellingIds.includes({{ $log->id }})">
                                                <svg class="animate-spin w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                            </template>
                                            <template x-if="!cancellingIds.includes({{ $log->id }})">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </template>
                                            <span x-text="cancellingIds.includes({{ $log->id }}) ? 'Đang hủy...' : 'Hủy'"></span>
                                        </button>

                                        {{-- Nút Tra cứu lại cho Pending với hiệu ứng xoay --}}
                                        <button type="button"
                                                @click="checkStatus({{ $log->id }}, '{{ route('admin.game-kicks.status', $log) }}')"
                                                :disabled="checkingIds.includes({{ $log->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-md shadow-sm transition disabled:opacity-60 cursor-pointer">
                                            <template x-if="checkingIds.includes({{ $log->id }})">
                                                <svg class="animate-spin w-3 h-3 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                            </template>
                                            <span x-text="checkingIds.includes({{ $log->id }}) ? 'Đang kiểm tra...' : 'Kiểm tra lại'"></span>
                                        </button>
                                    @endif

                                    {{-- Nút Kick lại nhanh với feedback highlight --}}
                                    <button type="button"
                                            @click="reloadForm('{{ $log->type }}', '{{ addslashes($log->name) }}', '{{ addslashes($log->reason ?? '') }}')"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md transition font-medium cursor-pointer">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        <span>Nạp lại</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    Chưa có lượt kick nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
        {{-- Floating Toast Notification --}}
        <div x-show="toastMessage"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-6 right-6 z-50 bg-slate-900/95 backdrop-blur text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 border border-slate-700 text-xs font-medium"
             style="display: none;">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span x-text="toastMessage"></span>
        </div>
    </div>

    {{-- Alpine Component Logic --}}
    <script>
        function gameKickApp() {
            return {
                hasPending: {{ $stats['pending'] > 0 ? 'true' : 'false' }},
                form: {
                    type: 'account',
                    name: '',
                    reason: ''
                },
                loading: false,
                isReloading: false,
                toastMessage: '',
                checkingIds: [],
                cancellingIds: [],
                cancellingAll: false,
                lastResult: null,
                pollTimer: null,
                pollCount: 0,

                init() {
                    // Nếu đang có tác vụ trong Queue (pending), kiểm tra ngầm nhẹ nhàng qua API, KHÔNG ép reload toàn trang
                    if (this.hasPending) {
                        this.startQueuePolling();
                    }
                },

                startQueuePolling() {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                    this.pollCount = 0;

                    this.pollTimer = setInterval(async () => {
                        this.pollCount++;
                        // Dừng sau 20 lần (khoảng 60 giây) nếu không có cập nhật để tránh tốn tài nguyên
                        if (this.pollCount > 20) {
                            clearInterval(this.pollTimer);
                            return;
                        }

                        try {
                            const res = await fetch('{{ route('admin.game-kicks.queue-status') }}', {
                                headers: { 'Accept': 'application/json' }
                            });
                            const data = await res.json();

                            // Khi hàng đợi đã được xử lý xong hoàn toàn (pending = 0), reload 1 lần duy nhất để xem kết quả
                            if (data.pending === 0 && this.hasPending) {
                                clearInterval(this.pollTimer);
                                this.hasPending = false;
                                this.showToast('✅ Hàng đợi đã xử lý xong!');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        } catch (e) {
                            // Bỏ qua lỗi tạm thời của request ngầm
                        }
                    }, 3000);
                },

                get parsedNames() {
                    if (!this.form.name) return [];
                    const list = this.form.name.split(/[,;\n\r]+/).map(s => s.trim()).filter(Boolean);
                    return [...new Set(list)];
                },

                get isBatch() {
                    return this.parsedNames.length > 1;
                },

                showToast(msg) {
                    this.toastMessage = msg;
                    setTimeout(() => {
                        if (this.toastMessage === msg) {
                            this.toastMessage = '';
                        }
                    }, 3500);
                },

                reloadForm(type, name, reason) {
                    this.form.type = type;
                    this.form.name = name;
                    this.form.reason = reason || '';
                    this.isReloading = true;
                    this.showToast(`Đã nạp lại thông tin: "${name}"`);

                    const card = document.getElementById('target-form-card');
                    if (card) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    setTimeout(() => {
                        const inputEl = document.getElementById('input-target-name');
                        if (inputEl) inputEl.focus();
                    }, 350);

                    setTimeout(() => {
                        this.isReloading = false;
                    }, 1200);
                },

                async checkStatus(logId, url) {
                    if (this.checkingIds.includes(logId)) return;
                    this.checkingIds.push(logId);
                    this.showToast('Đang kết nối kiểm tra trạng thái từ GameServer...');

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();
                        this.showToast((data.ok ? '✅ ' : 'ℹ️ ') + data.msg);

                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } catch (err) {
                        this.showToast('❌ Lỗi khi kiểm tra: ' + (err.message || 'Không có phản hồi'));
                    } finally {
                        this.checkingIds = this.checkingIds.filter(id => id !== logId);
                    }
                },

                async cancelAllQueue() {
                    if (!confirm('Bạn có chắc chắn muốn HỦY TOÀN BỘ các lệnh đang chờ trong hàng đợi không?')) {
                        return;
                    }

                    this.cancellingAll = true;
                    this.showToast('Đang gửi yêu cầu hủy toàn bộ hàng đợi...');

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch('{{ route('admin.game-kicks.cancel-all') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();
                        this.showToast(data.msg || 'Đã hủy toàn bộ hàng đợi');

                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } catch (err) {
                        this.showToast('❌ Lỗi khi hủy hàng đợi: ' + (err.message || 'Không có phản hồi'));
                    } finally {
                        this.cancellingAll = false;
                    }
                },

                async cancelSingle(logId, url) {
                    if (!confirm('Bạn có chắc chắn muốn hủy lệnh kick đang chờ này không?')) {
                        return;
                    }

                    if (this.cancellingIds.includes(logId)) return;
                    this.cancellingIds.push(logId);
                    this.showToast('Đang hủy lệnh...');

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();
                        this.showToast(data.msg || 'Đã hủy lệnh kick');

                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } catch (err) {
                        this.showToast('❌ Lỗi khi hủy: ' + (err.message || 'Không có phản hồi'));
                    } finally {
                        this.cancellingIds = this.cancellingIds.filter(id => id !== logId);
                    }
                },

                async submitKick() {
                    const names = this.parsedNames;
                    if (names.length === 0) return;

                    const isBatch = this.isBatch;
                    const confirmMsg = isBatch
                        ? `Bạn có chắc chắn muốn đưa ${names.length} tài khoản vào hàng đợi Queue để tự động kick ngầm?`
                        : `Bạn có chắc chắn muốn KICK ${this.form.type === 'account' ? 'tài khoản' : 'nhân vật'} "${names[0]}" khỏi toàn bộ GameServer?`;

                    if (!confirm(confirmMsg)) {
                        return;
                    }

                    this.loading = true;
                    this.lastResult = null;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch('{{ route('admin.game-kicks.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();
                        this.lastResult = data;

                        if (data.ok || data.code === '1' || data.is_batch) {
                            this.showToast(data.msg || 'Đã gửi yêu cầu thành công!');
                            // Reload trang sau 1s để hiển thị bản ghi mới
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            this.showToast(data.msg || 'Xử lý hoàn tất');
                        }
                    } catch (err) {
                        this.lastResult = {
                            ok: false,
                            code: '2',
                            result: 'network_error',
                            msg: 'Lỗi gửi yêu cầu: ' + (err.message || 'Không có phản hồi từ máy chủ')
                        };
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-admin-layout>
