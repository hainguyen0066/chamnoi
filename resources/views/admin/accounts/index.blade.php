<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span>Quản Lý Tài Khoản</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono font-semibold">Admin & User</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Top Summary Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng tài khoản</span>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total']) }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                    👥
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-rose-50/40">
                <div>
                    <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Quản trị viên (Admin)</span>
                    <div class="text-2xl font-bold text-rose-700 mt-1">{{ number_format($stats['admins']) }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                    🛡️
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-indigo-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-indigo-50/40">
                <div>
                    <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Người dùng (User)</span>
                    <div class="text-2xl font-bold text-indigo-700 mt-1">{{ number_format($stats['users']) }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                    👤
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Danh Sách Tài Khoản</h3>
                    <p class="text-xs text-slate-500">Quản trị viên có quyền tạo và quản lý tài khoản Admin và User trong hệ thống</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('admin.accounts.index') }}" class="flex items-center gap-2">
                        <select name="role" class="text-xs rounded-xl border-slate-200 bg-slate-50 py-2 pl-3 pr-8 focus:ring-rose-500 focus:border-rose-500 font-medium">
                            <option value="">-- Tất cả quyền --</option>
                            <option value="admin" @selected(request('role') === 'admin')>🛡️ Quản trị viên (Admin)</option>
                            <option value="user" @selected(request('role') === 'user')>👤 Người dùng (User)</option>
                        </select>

                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Tìm tên, email..."
                               class="text-xs rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:ring-rose-500 focus:border-rose-500 w-44">

                        <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                            Lọc
                        </button>
                    </form>

                    {{-- Add Account Button --}}
                    <a href="{{ route('admin.accounts.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-rose-600/20 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tạo Tài Khoản</span>
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Tài khoản</th>
                            <th class="py-3 px-4">Quyền hạn (Role)</th>
                            <th class="py-3 px-4">Trạng thái</th>
                            <th class="py-3 px-4">Ngày tạo</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($accounts as $acc)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs uppercase shrink-0 shadow-2xs {{ $acc->role === 'admin' ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ strtoupper(substr($acc->name ?: $acc->email, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm">{{ $acc->name }}</div>
                                            <div class="text-slate-400 font-mono text-[11px]">{{ $acc->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($acc->role === 'admin')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            🛡️ Quản trị viên
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            👤 Người dùng
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($acc->status === 'active')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Hoạt động
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Đã khóa
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ $acc->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-right space-x-1.5">
                                    {{-- Khóa/Mở khóa --}}
                                    @if (auth('admin')->user()?->email !== $acc->email)
                                        <form method="POST" action="{{ route('admin.accounts.toggle-status', $acc) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    onclick="return confirm('Bạn có chắc muốn {{ $acc->status === 'active' ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản {{ $acc->email }}?')"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-medium transition {{ $acc->status === 'active' ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                                {{ $acc->status === 'active' ? 'Khóa' : 'Mở' }}
                                            </button>
                                        </form>

                                        {{-- Xóa --}}
                                        <form method="POST" action="{{ route('admin.accounts.destroy', $acc) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Bạn có chắc chắn muốn XÓA vĩnh viễn tài khoản {{ $acc->email }}? Thao tác này không thể hoàn tác!')"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition">
                                                Xóa
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic px-2">Đang dùng</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Chưa có tài khoản nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($accounts->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
