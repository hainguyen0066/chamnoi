<x-admin-layout>
    <x-slot name="header">Quản lý đổi KPoint</x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Thống kê theo bộ lọc hiện tại --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach ([
            'completed' => ['Hoàn tất', 'text-green-700'],
            'pending'   => ['Chờ xử lý', 'text-yellow-700'],
            'failed'    => ['Thất bại (đã hoàn KPoint)', 'text-red-700'],
        ] as $st => [$label, $color])
            @php $row = $stats->get($st); @endphp
            <div class="bg-white shadow-sm rounded-lg p-4">
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-semibold {{ $color }}">
                    {{ number_format($row->total ?? 0) }} <span class="text-sm font-normal text-gray-500">giao dịch</span>
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ number_format($row->points ?? 0) }} KPoint → {{ number_format($row->knb ?? 0) }} KNB
                </p>
            </div>
        @endforeach
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('admin.knb-exchanges.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <span class="text-gray-400 text-sm">đến</span>
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="completed" @selected(request('status') === 'completed')>Hoàn tất</option>
                <option value="pending" @selected(request('status') === 'pending')>Chờ xử lý</option>
                <option value="failed" @selected(request('status') === 'failed')>Thất bại</option>
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tài khoản game / mã GD..."
                   class="rounded-md border-gray-300 shadow-sm text-sm w-56">
            <button type="submit" class="btn btn-dark">Lọc</button>
        </form>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Thời gian</th>
                    <th class="px-4 py-3">Mã GD</th>
                    <th class="px-4 py-3">Tài khoản game</th>
                    <th class="px-4 py-3">User web</th>
                    <th class="px-4 py-3 text-right">KPoint</th>
                    <th class="px-4 py-3 text-right">KNB</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($exchanges as $ex)
                    <tr>
                        <td class="px-4 py-3 text-gray-400">{{ $ex->id }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $ex->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $ex->txnid ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $ex->username }}</td>
                        <td class="px-4 py-3">
                            @if ($ex->user)
                                <a href="{{ route('admin.users.show', $ex->user) }}" class="text-indigo-600 hover:underline">
                                    {{ $ex->user->email ?? $ex->user->phone ?? ('#' . $ex->user_id) }}
                                </a>
                            @else
                                <span class="text-gray-400">#{{ $ex->user_id }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format($ex->point_amount) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($ex->knb_amount) }}</td>
                        <td class="px-4 py-3"><x-deposit-status :status="$ex->status" /></td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <a href="{{ route('admin.knb-exchanges.show', $ex) }}" class="text-indigo-600 hover:underline">Chi tiết</a>
                            @if ($ex->status === 'pending')
                                <form method="POST" action="{{ route('admin.knb-exchanges.retry', $ex) }}" class="inline"
                                      onsubmit="return confirm('Gọi lại API nạp KNB cho giao dịch #{{ $ex->id }}?');">
                                    @csrf
                                    <button type="submit" class="ml-3 text-amber-600 hover:underline">Thử lại</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Không có giao dịch nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $exchanges->links() }}</div>
</x-admin-layout>
