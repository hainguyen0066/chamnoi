<x-admin-layout>
    <x-slot name="header">Tạo tài khoản người dùng</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <p class="text-xs text-gray-400 mb-5">
                Tạo đồng thời tài khoản trên web và trong game. Không cần xác thực OTP, không bắt buộc email / số điện thoại.
                Chỉ lưu user trên web khi máy chủ game đã tạo tài khoản thành công.
            </p>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="f-name" class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                        <input type="text" id="f-name" name="name" value="{{ old('name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="f-birthday" class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                        <input type="date" id="f-birthday" name="birthday" value="{{ old('birthday') }}"
                               max="{{ now()->subYears(18)->toDateString() }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('birthday')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="f-username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                        <input type="text" id="f-username" name="username" value="{{ old('username') }}"
                               minlength="8" maxlength="16" autocomplete="off"
                               placeholder="8-16 ký tự, chỉ chữ cái và số"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('username')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="f-password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                        <input type="text" id="f-password" name="password" value="{{ old('password') }}"
                               autocomplete="off" placeholder="6-32 ký tự"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="f-email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-gray-400 font-normal">(không bắt buộc)</span>
                        </label>
                        <input type="email" id="f-email" name="email" value="{{ old('email') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="f-phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Số điện thoại <span class="text-gray-400 font-normal">(không bắt buộc)</span>
                        </label>
                        <input type="text" id="f-phone" name="phone" value="{{ old('phone') }}"
                               maxlength="20" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                        <div class="flex items-center gap-5 h-[38px]">
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="gender" value="1" @checked(old('gender') == 1)
                                       class="text-indigo-600 focus:ring-indigo-500">
                                Nam
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="gender" value="2" @checked(old('gender') == 2)
                                       class="text-indigo-600 focus:ring-indigo-500">
                                Nữ
                            </label>
                        </div>
                        @error('gender')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="f-address" class="block text-sm font-medium text-gray-700 mb-1">
                            Địa chỉ <span class="text-gray-400 font-normal">(không bắt buộc)</span>
                        </label>
                        <input type="text" id="f-address" name="address" value="{{ old('address') }}"
                               maxlength="255" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn btn-primary">
                        Tạo tài khoản
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        Huỷ
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
