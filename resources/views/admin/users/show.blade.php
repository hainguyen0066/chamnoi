<x-admin-layout>
    <x-slot name="header">Người dùng: {{ $user->name }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Hồ sơ --}}
        <div class="bg-white shadow-sm rounded-lg p-6 space-y-3">
            <h2 class="font-semibold text-gray-800 mb-2">Thông tin tài khoản</h2>
            <div class="text-sm"><span class="text-gray-500">Tên đăng nhập:</span> <span class="font-medium">{{ $user->username ?? '—' }}</span></div>
            <div class="text-sm flex items-center gap-2">
                <span class="text-gray-500">Mật khẩu:</span>
                @if ($user->plain_password !== null && $user->plain_password !== '')
                    <span id="pw-value" class="font-mono font-medium select-all" data-pw="{{ $user->plain_password }}">••••••••</span>
                    <button type="button" id="pw-toggle" class="btn-xs bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 focus:ring-gray-400">Hiện</button>
                    <button type="button" id="pw-copy" class="btn-xs bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 focus:ring-gray-400">Copy</button>
                @else
                    <span class="text-amber-600">Chưa lưu (tài khoản cũ) — sẽ có sau lần đổi mật khẩu kế tiếp.</span>
                @endif
            </div>
            <div class="text-sm"><span class="text-gray-500">Họ tên:</span> <span class="font-medium">{{ $user->name }}</span></div>
            <div class="text-sm"><span class="text-gray-500">Email:</span> <span class="font-medium">{{ $user->email ?? '—' }}</span></div>
            <div class="text-sm"><span class="text-gray-500">SĐT:</span> <span class="font-medium">{{ $user->phone ?? '—' }}</span></div>
            <div class="text-sm"><span class="text-gray-500">Ngày sinh:</span> <span class="font-medium">{{ $user->birthday?->format('d/m/Y') ?? '—' }}</span></div>
            <div class="text-sm"><span class="text-gray-500">Giới tính:</span> <span class="font-medium">{{ [1 => 'Nam', 2 => 'Nữ'][$user->gender] ?? '—' }}</span></div>
            <div class="text-sm"><span class="text-gray-500">Địa chỉ:</span> <span class="font-medium">{{ $user->address ?? '—' }}</span></div>
            <div class="text-sm">
                <span class="text-gray-500">Tài khoản game:</span>
                @if ($user->game_synced_at)
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">✔ Đã có (xác nhận {{ $user->game_synced_at->format('d/m/Y H:i') }})</span>
                @else
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">⚠ Chưa xác nhận</span>
                @endif
            </div>
            <div class="text-sm"><span class="text-gray-500">Ngày đăng ký:</span> <span class="font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="text-sm">
                <span class="text-gray-500">Trạng thái:</span>
                @if ($user->status === 'active')
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Hoạt động</span>
                @else
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Đã khoá</span>
                @endif
            </div>
            <div class="text-sm"><span class="text-gray-500">Số dư KPoint:</span>
                <span class="font-bold text-lg text-indigo-600">{{ number_format($user->xu_balance) }}</span>
            </div>

            <div class="pt-3 border-t flex flex-wrap gap-2">
                @if (! empty($user->username) && ! $user->game_synced_at && $user->plain_password !== null && $user->plain_password !== '')
                    <form method="POST" action="{{ route('admin.users.create-game-account', $user) }}"
                          onsubmit="return confirm('Tạo tài khoản game {{ $user->username }} với mật khẩu đang lưu trên web?');">
                        @csrf
                        <button type="submit" class="btn btn-success"
                                title="Dùng cho user đăng ký trước khi có API game. Nếu tài khoản đã có trong game sẽ báo trùng, không ảnh hưởng gì.">
                            Tạo tài khoản game
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    Sửa thông tin
                </a>
                <a href="{{ route('admin.users.changes', $user) }}" class="btn btn-secondary">
                    Chi tiết thay đổi thông tin
                </a>
                <a href="{{ route('admin.deposits.create') }}?account={{ urlencode($user->email ?? $user->phone) }}" class="btn btn-primary">
                    Nạp tiền cho user này
                </a>
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                      onsubmit="return confirm('{{ $user->status === 'active' ? 'Khoá' : 'Mở khoá' }} tài khoản này?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $user->status === 'active' ? 'btn-soft-danger' : 'btn-soft-success' }}">
                        {{ $user->status === 'active' ? 'Khoá tài khoản' : 'Mở khoá' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Đổi mật khẩu (web + trong game) --}}
        <div class="lg:col-span-3 bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-semibold text-gray-800 mb-1">Đổi mật khẩu</h2>
            <p class="text-xs text-gray-400 mb-4">
                Đổi đồng thời mật khẩu trên web và trong game. Chỉ báo thành công khi cả hai bên cùng đổi xong.
            </p>
            <form method="POST" action="{{ route('admin.users.change-password', $user) }}"
                  onsubmit="return confirm('Đổi mật khẩu tài khoản {{ $user->username }}?');"
                  class="flex flex-wrap items-start gap-3">
                @csrf
                <div>
                    <input type="text" name="password" value="{{ old('password') }}"
                           placeholder="Mật khẩu mới (6-32 ký tự)" autocomplete="off"
                           class="w-72 rounded-md border-gray-300 shadow-sm text-sm">
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" @disabled(empty($user->username))>
                    Đổi mật khẩu
                </button>
                @if (empty($user->username))
                    <p class="text-sm text-amber-600 self-center">Tài khoản chưa có tên đăng nhập game.</p>
                @endif
            </form>
        </div>

        @if ($user->plain_password !== null && $user->plain_password !== '')
        <script>
            (function () {
                var v = document.getElementById('pw-value'), t = document.getElementById('pw-toggle'), c = document.getElementById('pw-copy');
                var shown = false;
                t.addEventListener('click', function () {
                    shown = !shown;
                    v.textContent = shown ? v.dataset.pw : '••••••••';
                    t.textContent = shown ? 'Ẩn' : 'Hiện';
                });
                c.addEventListener('click', function () {
                    navigator.clipboard.writeText(v.dataset.pw).then(function () {
                        c.textContent = 'Đã copy'; setTimeout(function () { c.textContent = 'Copy'; }, 1500);
                    });
                });
            })();
        </script>
        @endif

        {{-- Lịch sử nạp --}}
        <div class="lg:col-span-2 bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-800">Lịch sử nạp tiền</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-left">
                    <tr>
                        <th class="px-5 py-3">Thời gian</th>
                        <th class="px-5 py-3">Phương thức</th>
                        <th class="px-5 py-3 text-right">Số tiền</th>
                        <th class="px-5 py-3 text-right">KM</th>
                        <th class="px-5 py-3 text-right">KPoint nhận</th>
                        <th class="px-5 py-3">Trạng thái</th>
                        <th class="px-5 py-3">Người xử lý</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($deposits as $deposit)
                        <tr>
                            <td class="px-5 py-3 text-gray-500">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3">{{ \App\Models\Deposit::METHODS[$deposit->method] ?? $deposit->method }}</td>
                            <td class="px-5 py-3 text-right">{{ number_format($deposit->amount) }} đ</td>
                            <td class="px-5 py-3 text-right">{{ $deposit->promotion_percent }}%</td>
                            <td class="px-5 py-3 text-right font-medium">{{ number_format($deposit->amount_received) }}</td>
                            <td class="px-5 py-3"><x-deposit-status :status="$deposit->status" /></td>
                            <td class="px-5 py-3 text-gray-500">{{ $deposit->processedBy?->name ?? 'Hệ thống' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400">Chưa có giao dịch nạp nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $deposits->links() }}</div>
        </div>
    </div>
</x-admin-layout>
