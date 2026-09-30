<x-admin-layout>
    <x-slot name="header">Quản lý nạp tiền</x-slot>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.deposits.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <span class="text-gray-400 text-sm">đến</span>
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <select name="method" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả phương thức</option>
                @foreach (\App\Models\Deposit::METHODS as $value => $label)
                    <option value="{{ $value }}" @selected(request('method') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="completed" @selected(request('status') === 'completed')>Hoàn tất</option>
                <option value="pending" @selected(request('status') === 'pending')>Chờ xử lý</option>
                <option value="failed" @selected(request('status') === 'failed')>Thất bại</option>
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tài khoản..."
                   class="rounded-md border-gray-300 shadow-sm text-sm w-40">
            <button type="submit" class="btn btn-dark">Lọc</button>
        </form>

        <a href="{{ route('admin.deposits.create') }}" class="btn btn-primary">
            + Nạp tiền cho user
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Thời gian</th>
                    <th class="px-4 py-3">Tài khoản</th>
                    <th class="px-4 py-3">Loại</th>
                    <th class="px-4 py-3">Phương thức</th>
                    <th class="px-4 py-3 text-right">Số tiền</th>
                    <th class="px-4 py-3 text-right">KM</th>
                    <th class="px-4 py-3 text-right">KPoint nhận</th>
                    <th class="px-4 py-3">Nội dung</th>
                    <th class="px-4 py-3">Nguồn</th>
                    <th class="px-4 py-3">Người xử lý</th>
                    <th class="px-4 py-3">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($deposits as $deposit)
                    <tr>
                        <td class="px-4 py-3 text-gray-400">{{ $deposit->id }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @if ($deposit->user)
                                <a href="{{ route('admin.users.show', $deposit->user) }}" class="text-indigo-600 hover:underline">
                                    {{ $deposit->account }}
                                </a>
                            @else
                                {{ $deposit->account }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ \App\Models\Deposit::TYPES[$deposit->type] ?? $deposit->type }}</td>
                        <td class="px-4 py-3">{{ \App\Models\Deposit::METHODS[$deposit->method] ?? $deposit->method }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($deposit->amount) }} đ</td>
                        <td class="px-4 py-3 text-right">{{ $deposit->promotion_percent }}%</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($deposit->amount_received) }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-[180px] truncate" title="{{ $deposit->note }}">{{ $deposit->note ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($deposit->source === 'manual')
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">Nhập tay</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700">Gateway</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $deposit->processedBy?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><x-deposit-status :status="$deposit->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="px-4 py-6 text-center text-gray-400">Không có giao dịch nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $deposits->links() }}</div>
</x-admin-layout>
