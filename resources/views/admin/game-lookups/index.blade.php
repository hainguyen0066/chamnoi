<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Tra Cứu Nhân Vật & Tài Khoản</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 font-mono font-semibold">GameServer API v2</span>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="gameLookupApp()">
        {{-- Top Summary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tổng lượt tra cứu</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</span>
                    <span class="text-xs text-slate-400">lịch sử</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-emerald-50/40">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Tìm thấy thành công</span>
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
                <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Không tìm thấy</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-amber-700">{{ number_format($stats['not_found']) }}</span>
                    <span class="text-xs text-amber-600">Not Found</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-sky-100 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-sky-50/40">
                <span class="text-xs font-semibold text-sky-700 uppercase tracking-wider">Tra cứu hôm nay</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-bold text-sky-700">{{ number_format($stats['today']) }}</span>
                    <span class="text-xs text-sky-500">Hôm nay</span>
                </div>
            </div>
        </div>

        {{-- Main Lookup Form Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Tra Cứu: Tên Nhân Vật ⇄ Tài Khoản</h2>
                        <p class="text-xs text-slate-500">Tra cứu 1 hoặc nhiều tài khoản / nhân vật cùng lúc từ GameServer</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitSearch" class="p-6 space-y-5">
                {{-- Mode Selector --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                        Phương thức tra cứu <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label :class="form.by === 'role' ? 'border-sky-500 bg-sky-50/60 ring-2 ring-sky-500/20 text-sky-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                               class="relative flex items-center p-3.5 border rounded-xl cursor-pointer transition">
                            <input type="radio" value="role" x-model="form.by" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                     :class="form.by === 'role' ? 'border-sky-600 bg-sky-600' : 'border-slate-300'">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="form.by === 'role'"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">Tên Nhân Vật ➔ Tìm Tài Khoản</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Nhập tên nhân vật (có dấu), trả về tài khoản sở hữu</div>
                                </div>
                            </div>
                        </label>

                        <label :class="form.by === 'account' ? 'border-sky-500 bg-sky-50/60 ring-2 ring-sky-500/20 text-sky-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                               class="relative flex items-center p-3.5 border rounded-xl cursor-pointer transition">
                            <input type="radio" value="account" x-model="form.by" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                     :class="form.by === 'account' ? 'border-sky-600 bg-sky-600' : 'border-slate-300'">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="form.by === 'account'"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">Tài Khoản ➔ Tìm Các Nhân Vật</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Nhập tài khoản, trả về danh sách tất cả nhân vật</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Input Field (Hỗ trợ 1 hoặc nhiều mục) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <span x-text="form.by === 'role' ? 'Tên Nhân Vật Cần Tra' : 'Tên Tài Khoản Cần Tra'"></span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Nhập 1 tên hoặc nhiều tên phân cách bằng dấu phẩy <code>,</code> hoặc xuống dòng</span>
                    </div>

                    <div class="relative">
                        <textarea
                            x-model="form.names"
                            :rows="parsedNames.length > 2 ? 4 : 2"
                            :placeholder="form.by === 'role' ? 'Vd: Độc_Cô hoặc nhiều tên: Độc_Cô, Long Ngũ, Dương Quá...' : 'Vd: dang01234 hoặc nhiều tài khoản: dang01234, 0868465426...'"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800 placeholder-slate-400 font-medium text-sm transition resize-y"
                            required
                        ></textarea>
                        <button type="button" @click="form.names = ''" x-show="form.names" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 p-1 text-xs">
                            ✕ Xoá
                        </button>
                    </div>

                    {{-- Preview Chips khi phát hiện nhiều mục --}}
                    <template x-if="parsedNames.length > 1">
                        <div class="mt-2.5 p-3 rounded-xl bg-slate-50 border border-sky-200">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-700 mb-1.5">
                                <span class="flex items-center gap-1.5 text-sky-700">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    Đã nhận diện <strong x-text="parsedNames.length"></strong> mục cần tra cứu
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto">
                                <template x-for="(nameItem, idx) in parsedNames" :key="idx">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-white border border-slate-200 text-slate-800 shadow-2xs">
                                        <span class="text-sky-600 font-bold" x-text="'#' + (idx + 1)"></span>
                                        <span x-text="nameItem"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center gap-3 pt-1">
                    <button
                        type="submit"
                        :disabled="loading || !form.names.trim()"
                        class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 shadow-md shadow-sky-600/20 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 cursor-pointer"
                    >
                        <svg x-show="loading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg x-show="!loading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <span x-text="loading ? 'Đang tra cứu từ GameServer...' : (parsedNames.length > 1 ? 'Tra Cứu ' + parsedNames.length + ' Mục' : 'Tra Cứu Ngay')"></span>
                    </button>
                </div>
            </form>

            {{-- KHU VỰC HIỂN THỊ KẾT QUẢ TRA CỨU --}}
            <div x-show="resultsList.length > 0" x-transition class="border-t border-slate-200 bg-slate-50/50 p-6 space-y-4">
                {{-- TRƯỜNG HỢP 1: Tra Cứu 1 Mục Duy Nhất --}}
                <template x-if="resultsList.length === 1">
                    <div class="p-4 rounded-xl border transition"
                         :class="resultsList[0].ok ? 'bg-emerald-50/60 border-emerald-200' : 'bg-rose-50/60 border-rose-200'">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs uppercase font-bold px-2 py-0.5 rounded"
                                          :class="resultsList[0].ok ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                          x-text="resultsList[0].ok ? 'Tìm thấy' : resultsList[0].result"></span>
                                    <span class="text-sm font-semibold text-slate-700" x-text="resultsList[0].msg"></span>
                                </div>

                                {{-- Nếu tra theo Role --}}
                                <template x-if="resultsList[0].by === 'role'">
                                    <div class="mt-2 text-sm">
                                        <span class="text-slate-500">Nhân vật:</span>
                                        <strong class="text-slate-900 font-bold ml-1 font-mono text-base" x-text="resultsList[0].name"></strong>
                                        <span class="mx-2 text-slate-300">➔</span>
                                        <span class="text-slate-500">Tài khoản:</span>
                                        <template x-if="resultsList[0].account">
                                            <span class="inline-flex items-center gap-2 ml-1">
                                                <strong class="text-sky-700 font-bold font-mono text-base bg-sky-100/80 px-2.5 py-0.5 rounded" x-text="resultsList[0].account"></strong>
                                                <button type="button" @click="copyText(resultsList[0].account)" class="text-xs text-sky-600 hover:text-sky-800 font-semibold underline">Copy</button>
                                            </span>
                                        </template>
                                        <template x-if="!resultsList[0].account">
                                            <span class="text-rose-500 italic ml-1">Không có tài khoản</span>
                                        </template>
                                    </div>
                                </template>

                                {{-- Nếu tra theo Account --}}
                                <template x-if="resultsList[0].by === 'account'">
                                    <div class="mt-2 text-sm">
                                        <span class="text-slate-500">Tài khoản:</span>
                                        <strong class="text-slate-900 font-bold ml-1 font-mono text-base" x-text="resultsList[0].name"></strong>
                                        <span class="mx-2 text-slate-300">➔</span>
                                        <span class="text-slate-500">Danh sách nhân vật:</span>
                                        <template x-if="resultsList[0].roles && resultsList[0].roles.length > 0">
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <template x-for="(role, idx) in resultsList[0].roles" :key="idx">
                                                    <span class="inline-flex items-center gap-1.5 bg-white border border-slate-200 shadow-sm rounded-lg px-2.5 py-1 text-xs font-mono font-semibold text-slate-800">
                                                        <span x-text="role"></span>
                                                        <button type="button" @click="copyText(role)" title="Sao chép" class="text-slate-400 hover:text-slate-700">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                        </button>
                                                        <a :href="'{{ route('admin.game-kicks.index') }}?type=role&name=' + encodeURIComponent(role)" title="Kick nhân vật này" class="text-rose-500 hover:text-rose-700">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                        </a>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!resultsList[0].roles || resultsList[0].roles.length === 0">
                                            <span class="text-slate-400 italic ml-1">Chưa tạo nhân vật nào</span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 shrink-0">
                                <template x-if="resultsList[0].ok && resultsList[0].by === 'role' && resultsList[0].account">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="lookupReverse(resultsList[0].account)" class="px-3 py-1.5 rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 text-xs font-semibold transition">
                                            Xem các NV khác của acc này
                                        </button>
                                        <a :href="'{{ route('admin.game-kicks.index') }}?type=account&name=' + encodeURIComponent(resultsList[0].account)" class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200 text-xs font-semibold transition flex items-center gap-1">
                                            Kick Acc này
                                        </a>
                                    </div>
                                </template>

                                <template x-if="resultsList[0].ok && resultsList[0].by === 'account'">
                                    <a :href="'{{ route('admin.game-kicks.index') }}?type=account&name=' + encodeURIComponent(resultsList[0].name)" class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200 text-xs font-semibold transition flex items-center gap-1">
                                        Kick Acc này
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- TRƯỜNG HỢP 2: Tra Cứu Hàng Loạt (Nhiều Mục) --}}
                <template x-if="resultsList.length > 1">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                <span>Kết Quả Tra Cứu Hàng Loạt</span>
                                <span class="text-xs font-mono font-medium px-2 py-0.5 rounded-full bg-sky-100 text-sky-800" x-text="resultsList.length + ' kết quả'"></span>
                            </h3>
                            <button type="button" @click="copyAllResults()" class="text-xs px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-xs font-medium transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copiedAll ? '✓ Đã sao chép' : 'Sao chép tất cả kết quả'"></span>
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                                        <th class="p-3">#</th>
                                        <th class="p-3">Đối tượng tra</th>
                                        <th class="p-3">Loại</th>
                                        <th class="p-3">Tài khoản</th>
                                        <th class="p-3">Danh sách nhân vật</th>
                                        <th class="p-3">Trạng thái</th>
                                        <th class="p-3 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono">
                                    <template x-for="(item, idx) in resultsList" :key="idx">
                                        <tr class="hover:bg-slate-50 transition" :class="item.ok ? '' : 'bg-rose-50/20'">
                                            <td class="p-3 text-slate-400" x-text="idx + 1"></td>
                                            <td class="p-3 font-bold text-slate-900" x-text="item.name"></td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase"
                                                      :class="item.by === 'role' ? 'bg-sky-100 text-sky-700' : 'bg-indigo-100 text-indigo-700'"
                                                      x-text="item.by === 'role' ? 'Nhân vật' : 'Tài khoản'"></span>
                                            </td>
                                            <td class="p-3">
                                                <template x-if="item.account">
                                                    <span class="font-bold text-sky-700" x-text="item.account"></span>
                                                </template>
                                                <template x-if="!item.account">
                                                    <span class="text-slate-300">—</span>
                                                </template>
                                            </td>
                                            <td class="p-3 max-w-xs truncate" :title="item.roles ? item.roles.join(', ') : ''">
                                                <template x-if="item.roles && item.roles.length > 0">
                                                    <span class="text-slate-800" x-text="item.roles.join(', ')"></span>
                                                </template>
                                                <template x-if="!item.roles || item.roles.length === 0">
                                                    <span class="text-slate-300">—</span>
                                                </template>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                      :class="item.ok ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                                      x-text="item.ok ? 'Tìm thấy' : item.result"></span>
                                            </td>
                                            <td class="p-3 text-right">
                                                <template x-if="item.account">
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <button type="button" @click="copyText(item.account)" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Copy</button>
                                                        <span class="text-slate-300">|</span>
                                                        <a :href="'{{ route('admin.game-kicks.index') }}?type=account&name=' + encodeURIComponent(item.account)" class="text-xs text-rose-600 hover:text-rose-800 font-medium">Kick</a>
                                                    </div>
                                                </template>
                                                <template x-if="!item.account && item.roles && item.roles.length > 0">
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <button type="button" @click="copyText(item.roles.join(', '))" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Copy NV</button>
                                                        <span class="text-slate-300">|</span>
                                                        <a :href="'{{ route('admin.game-kicks.index') }}?type=account&name=' + encodeURIComponent(item.name)" class="text-xs text-rose-600 hover:text-rose-800 font-medium">Kick</a>
                                                    </div>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Lịch Sử Tra Cứu Gần Đây --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold text-slate-800">Nhật Ký Tra Cứu Gần Đây</h3>
                    <span class="text-xs text-slate-400 font-mono">({{ $logs->total() }} bản ghi)</span>
                </div>

                <form method="GET" action="{{ route('admin.game-lookups.index') }}" class="flex items-center gap-2 flex-wrap text-xs">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên/acc..."
                           class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500">

                    <select name="by" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-700 bg-white">
                        <option value="">Tất cả loại</option>
                        <option value="role" {{ request('by') === 'role' ? 'selected' : '' }}>Tên nhân vật</option>
                        <option value="account" {{ request('by') === 'account' ? 'selected' : '' }}>Tài khoản</option>
                    </select>

                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-900 transition">
                        Lọc
                    </button>
                    @if(request()->hasAny(['search', 'by', 'result']))
                        <a href="{{ route('admin.game-lookups.index') }}" class="px-2 py-1.5 text-slate-500 hover:text-slate-800">Xoá lọc</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200/80 text-slate-500 font-semibold uppercase tracking-wider">
                            <th class="p-3.5">Thời gian</th>
                            <th class="p-3.5">Loại tra</th>
                            <th class="p-3.5">Giá trị tra</th>
                            <th class="p-3.5">Tài khoản</th>
                            <th class="p-3.5">Nhân vật</th>
                            <th class="p-3.5">Trạng thái</th>
                            <th class="p-3.5">Admin</th>
                            <th class="p-3.5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        {{-- New logs in this session --}}
                        <template x-for="(liveLog, lIdx) in newLiveLogs" :key="'live-' + lIdx">
                            <tr class="bg-sky-50/40 hover:bg-sky-50 transition border-l-4 border-l-sky-500">
                                <td class="p-3.5 text-slate-500 whitespace-nowrap flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                    <span x-text="liveLog.time"></span>
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-sky-200 text-sky-800 font-bold uppercase">Mới</span>
                                </td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase"
                                          :class="liveLog.by === 'role' ? 'bg-sky-100 text-sky-700' : 'bg-indigo-100 text-indigo-700'"
                                          x-text="liveLog.by_text"></span>
                                </td>
                                <td class="p-3.5 font-bold text-slate-900 whitespace-nowrap" x-text="liveLog.query_name"></td>
                                <td class="p-3.5 text-slate-700 whitespace-nowrap">
                                    <template x-if="liveLog.account">
                                        <span class="font-bold text-sky-700" x-text="liveLog.account"></span>
                                    </template>
                                    <template x-if="!liveLog.account">
                                        <span class="text-slate-300">—</span>
                                    </template>
                                </td>
                                <td class="p-3.5 text-slate-700 max-w-xs truncate" :title="liveLog.roles ? liveLog.roles.join(', ') : ''">
                                    <template x-if="liveLog.roles && liveLog.roles.length > 0">
                                        <span x-text="liveLog.roles.join(', ')"></span>
                                    </template>
                                    <template x-if="!liveLog.roles || liveLog.roles.length === 0">
                                        <span class="text-slate-300">—</span>
                                    </template>
                                </td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                          :class="liveLog.ok ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200'"
                                          x-text="liveLog.ok ? 'Tìm thấy' : liveLog.result"></span>
                                </td>
                                <td class="p-3.5 text-slate-500 whitespace-nowrap" x-text="liveLog.admin_name"></td>
                                <td class="p-3.5 text-right whitespace-nowrap">
                                    <template x-if="liveLog.account">
                                        <a :href="'{{ route('admin.game-kicks.index') }}?type=account&name=' + encodeURIComponent(liveLog.account)"
                                           class="inline-flex items-center gap-1 text-[11px] text-rose-600 hover:text-rose-800 font-semibold">
                                            <span>Kick</span>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        @forelse ($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition {{ $log->isSuccess() ? '' : 'bg-slate-50/30' }}">
                                <td class="p-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $log->by === 'role' ? 'bg-sky-100 text-sky-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $log->by_text }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-bold text-slate-900 whitespace-nowrap">
                                    {{ $log->query_name }}
                                </td>
                                <td class="p-3.5 text-slate-700 whitespace-nowrap">
                                    @if ($log->account)
                                        <span class="font-bold text-sky-700">{{ $log->account }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-slate-700 max-w-xs truncate" title="{{ is_array($log->roles) ? implode(', ', $log->roles) : '' }}">
                                    @if (!empty($log->roles))
                                        <span>{{ implode(', ', $log->roles) }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $log->result_badge }}">
                                        {{ $log->isSuccess() ? 'Tìm thấy' : ($log->isNotFound() ? 'Không có' : $log->result) }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $log->admin_name ?: ($log->admin?->name ?? 'System') }}
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap">
                                    @if ($log->account)
                                        <a href="{{ route('admin.game-kicks.index') }}?type=account&name={{ urlencode($log->account) }}"
                                           class="inline-flex items-center gap-1 text-[11px] text-rose-600 hover:text-rose-800 font-semibold">
                                            <span>Kick</span>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400 font-sans">
                                    Chưa có nhật ký tra cứu nào.
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
    </div>

    {{-- Script đặt trực tiếp để Alpine nhận diện ngay lập tức 100% --}}
    <script>
        function gameLookupApp() {
            return {
                form: {
                    by: 'role',
                    names: '',
                },
                loading: false,
                resultsList: [],
                newLiveLogs: [],
                copiedAll: false,

                get parsedNames() {
                    if (!this.form.names) return [];
                    const list = this.form.names.split(/[,;\n\r]+/).map(s => s.trim()).filter(Boolean);
                    return [...new Set(list)];
                },

                async submitSearch() {
                    const term = (this.form.names || '').trim();
                    if (!term || this.loading) return;

                    this.loading = true;
                    this.copiedAll = false;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const response = await fetch('{{ route('admin.game-lookups.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                by: this.form.by,
                                names: term,
                            }),
                        });

                        const data = await response.json();
                        if (response.ok && data.results && data.results.length > 0) {
                            // Lưu toàn bộ danh sách kết quả (kể cả 1 hay nhiều mục)
                            this.resultsList = data.results;

                            // Đưa toàn bộ các kết quả mới vào đầu bảng nhật ký
                            data.results.forEach(item => {
                                this.newLiveLogs.unshift({
                                    time: (new Date()).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                                    by: item.by,
                                    by_text: item.by === 'role' ? 'Tên nhân vật' : 'Tài khoản',
                                    query_name: item.name,
                                    account: item.account,
                                    roles: item.roles || [],
                                    ok: item.ok,
                                    result: item.result,
                                    admin_name: 'Bạn',
                                });
                            });
                        } else {
                            alert(data.msg || 'Có lỗi xảy ra khi tra cứu.');
                        }
                    } catch (error) {
                        alert('Không thể kết nối đến máy chủ: ' + error.message);
                    } finally {
                        this.loading = false;
                    }
                },

                lookupReverse(account) {
                    this.form.by = 'account';
                    this.form.names = account;
                    this.submitSearch();
                },

                copyText(text) {
                    if (!text) return;
                    navigator.clipboard.writeText(text).then(() => {
                        alert('Đã sao chép: ' + text);
                    }).catch(() => {
                        prompt('Sao chép giá trị:', text);
                    });
                },

                copyAllResults() {
                    if (!this.resultsList.length) return;
                    const lines = this.resultsList.map(r => {
                        if (r.by === 'role') {
                            return `${r.name}\t${r.account || 'KHÔNG_CÓ'}`;
                        } else {
                            return `${r.name}\t${(r.roles || []).join(', ') || 'KHÔNG_CÓ'}`;
                        }
                    }).join('\n');

                    navigator.clipboard.writeText(lines).then(() => {
                        this.copiedAll = true;
                        setTimeout(() => this.copiedAll = false, 2500);
                    }).catch(() => {
                        prompt('Sao chép kết quả:', lines);
                    });
                }
            };
        }
    </script>
</x-admin-layout>
