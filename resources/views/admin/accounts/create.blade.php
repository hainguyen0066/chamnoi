<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.accounts.index') }}" class="text-slate-400 hover:text-slate-600 transition">Tài Khoản</a>
            <span class="text-slate-300">/</span>
            <span>Tạo Tài Khoản Mới</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-lg shadow-2xs">
                        👤
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Đăng Ký Tài Khoản Mới</h2>
                        <p class="text-xs text-slate-500">Chỉ quản trị viên mới có quyền tạo thêm tài khoản Admin hoặc User</p>
                    </div>
                </div>

                <a href="{{ route('admin.accounts.index') }}"
                   class="text-xs text-slate-500 hover:text-slate-800 font-medium px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition">
                    ← Quay lại
                </a>
            </div>

            <form method="POST" action="{{ route('admin.accounts.store') }}" class="p-6 space-y-5" x-data="{ role: '{{ old('role', 'user') }}' }">
                @csrf

                {{-- Role Selection (Trực quan Admin / User) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                        Quyền hạn (Role) <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label :class="role === 'admin' ? 'border-rose-500 bg-rose-50/50 ring-2 ring-rose-500/20 text-rose-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                               class="relative flex items-center p-3.5 border rounded-xl cursor-pointer transition">
                            <input type="radio" name="role" value="admin" x-model="role" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center"
                                     :class="role === 'admin' ? 'border-rose-600 bg-rose-600' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="role === 'admin'"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-bold flex items-center gap-1.5">
                                        <span>🛡️ Quản trị viên (Admin)</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Có quyền đăng nhập trang Admin Panel & kick người chơi</div>
                                </div>
                            </div>
                        </label>

                        <label :class="role === 'user' ? 'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20 text-indigo-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                               class="relative flex items-center p-3.5 border rounded-xl cursor-pointer transition">
                            <input type="radio" name="role" value="user" x-model="role" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center"
                                     :class="role === 'user' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="role === 'user'"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-bold flex items-center gap-1.5">
                                        <span>👤 Người dùng (User)</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Tài khoản thành viên thông thường</div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        Họ và tên / Tên hiển thị <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Ví dụ: Nguyễn Văn A, Admin Long..."
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 font-medium transition text-sm">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        Địa chỉ Email đăng nhập <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="Ví dụ: admin02@volam.local hoặc user@gmail.com"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 font-medium transition text-sm">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password & Confirm Password in 2 cols --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                            Mật khẩu <span class="text-rose-500">*</span>
                        </label>
                        <input type="password"
                               name="password"
                               required
                               placeholder="Tối thiểu 6 ký tự"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 font-medium transition text-sm">
                        @error('password')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                            Nhập lại mật khẩu <span class="text-rose-500">*</span>
                        </label>
                        <input type="password"
                               name="password_confirmation"
                               required
                               placeholder="Xác nhận mật khẩu"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 font-medium transition text-sm">
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('admin.accounts.index') }}"
                       class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-sm transition">
                        Hủy
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-rose-600/25 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" />
                        </svg>
                        <span>Tạo Tài Khoản Ngay</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
