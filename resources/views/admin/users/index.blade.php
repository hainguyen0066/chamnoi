<x-admin-layout>
    <x-slot name="header">Người dùng</x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Tạo tài khoản
        </a>
    </div>

    {{-- Bước 2: hỏi checkuser.php cho user chưa đánh dấu (theo bộ lọc q/status đang xem), chỉ đánh dấu, không tạo/đổi gì --}}
    @php $pending = $gameStats['none']; $synced = $gameStats['total'] - $gameStats['none']; @endphp
    <div class="mb-5 rounded-lg border-2 p-4 md:p-5 flex flex-wrap items-center gap-4 shadow-sm
                {{ $pending > 0 ? 'bg-amber-50 border-amber-400' : 'bg-emerald-50 border-emerald-300' }}">
        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center
                    {{ $pending > 0 ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }}">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                @if ($pending > 0)
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
        </div>

        <div class="flex-1 min-w-[220px]">
            <h2 class="font-bold text-base {{ $pending > 0 ? 'text-amber-800' : 'text-emerald-800' }}">
                Kiểm tra tài khoản Ingame
            </h2>
            <p class="text-sm mt-0.5 {{ $pending > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                @if ($pending > 0)
                    Còn <strong class="text-lg">{{ number_format($pending) }}</strong> / {{ number_format($gameStats['total']) }} user
                    <strong>chưa xác nhận</strong> có tài khoản trong game.
                @else
                    Tất cả {{ number_format($gameStats['total']) }} user đã được xác nhận có tài khoản trong game.
                @endif
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Hỏi máy chủ game (<code>checkuser.php</code>) cho tối đa 50 user chưa đánh dấu mỗi lần bấm, theo bộ lọc tìm kiếm/trạng thái đang xem.
                Chỉ <strong>đánh dấu</strong> user đã có tài khoản — không tạo tài khoản, không đổi mật khẩu.
            </p>
            <div class="text-xs mt-1.5 flex flex-wrap gap-3">
                <a href="{{ route('admin.users.index', ['game' => 'none']) }}" class="text-amber-700 hover:underline font-medium">⚠ Xem {{ number_format($pending) }} user chưa xác nhận</a>
                <a href="{{ route('admin.users.index', ['game' => 'ok']) }}" class="text-emerald-700 hover:underline font-medium">✔ Xem {{ number_format($synced) }} user đã có</a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.check-game') }}" class="flex-shrink-0"
              onsubmit="return confirm('Kiểm tra tài khoản game cho tối đa 50 user chưa đánh dấu (theo bộ lọc hiện tại)?');">
            @csrf
            <input type="hidden" name="q" value="{{ request('q') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit"
                    class="btn text-base
                           {{ $pending > 0
                               ? 'bg-emerald-600 hover:bg-emerald-700 text-white ring-2 ring-emerald-300 ring-offset-2 ring-offset-amber-50 animate-pulse hover:animate-none focus:ring-emerald-500'
                               : 'bg-gray-200 text-gray-500 cursor-not-allowed shadow-none' }}"
                    @disabled($pending === 0)
                    title="Chỉ kiểm tra và đánh dấu user đã có tài khoản trong game. Không tạo tài khoản, không đổi mật khẩu.">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Check khoản Ingame
                @if ($pending > 0)
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-white/25 text-sm">{{ number_format($pending) }}</span>
                @endif
            </button>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap items-center gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm username / tên / email / SĐT..."
               class="rounded-md border-gray-300 shadow-sm text-sm w-64">
        <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
            <option value="">Tất cả trạng thái</option>
            <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
            <option value="locked" @selected(request('status') === 'locked')>Đã khoá</option>
        </select>
        <select name="game" class="rounded-md border-gray-300 shadow-sm text-sm">
            <option value="">TK game: tất cả</option>
            <option value="none" @selected(request('game') === 'none')>⚠ Chưa có TK game</option>
            <option value="ok" @selected(request('game') === 'ok')>✔ Đã có TK game</option>
        </select>
        <button type="submit" class="btn btn-dark">Lọc</button>
    </form>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3">ID</th>
                    <th class="px-5 py-3">Tên</th>
					<th class="px-5 py-3">Username</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">SĐT</th>
                    <th class="px-5 py-3 text-right">Số dư KPoint</th>
                    <th class="px-5 py-3">Ngày đăng ký</th>
                    <th class="px-5 py-3">TK game</th>
                    <th class="px-5 py-3">Trạng thái</th>
                    <th class="px-5 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-5 py-3 text-gray-500">{{ $user->id }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $user->name }}</td>
						<td class="px-5 py-3 font-medium text-gray-800">{{ $user->username }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $user->email ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $user->phone ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-medium">{{ number_format($user->xu_balance) }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            @if ($user->game_synced_at)
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700" title="Xác nhận {{ $user->game_synced_at->format('d/m/Y H:i') }}">✔ Đã có</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">⚠ Chưa có</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if ($user->status === 'active')
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Hoạt động</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Đã khoá</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-xs btn-xs-primary">Chi tiết</a>
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline-block ml-2"
                                  onsubmit="return confirm('{{ $user->status === 'active' ? 'Khoá' : 'Mở khoá' }} tài khoản {{ $user->username ?? $user->email }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-xs {{ $user->status === 'active' ? 'btn-xs-danger' : 'btn-xs-success' }}">
                                    {{ $user->status === 'active' ? 'Khoá' : 'Mở khoá' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-5 py-6 text-center text-gray-400">Không tìm thấy người dùng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
