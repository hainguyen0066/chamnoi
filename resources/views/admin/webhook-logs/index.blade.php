<x-admin-layout>
    <x-slot name="header">Log Webhook Thanh Toán</x-slot>

    <div class="mb-4">
        <form method="GET" action="{{ route('admin.webhook-logs.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <span class="text-gray-400 text-sm">đến</span>
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <select name="gateway" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả cổng</option>
                @foreach (\App\Models\PaymentWebhookLog::GATEWAYS as $value => $label)
                    <option value="{{ $value }}" @selected(request('gateway') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="direction" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả chiều</option>
                @foreach (\App\Models\PaymentWebhookLog::DIRECTIONS as $value => $label)
                    <option value="{{ $value }}" @selected(request('direction') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="event" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tất cả loại</option>
                @foreach (\App\Models\PaymentWebhookLog::EVENTS as $value => $label)
                    <option value="{{ $value }}" @selected(request('event') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="sig" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Chữ ký: tất cả</option>
                <option value="1" @selected(request('sig') === '1')>Hợp lệ</option>
                <option value="0" @selected(request('sig') === '0')>Sai chữ ký</option>
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Mã giao dịch..."
                   class="rounded-md border-gray-300 shadow-sm text-sm w-44">
            <button type="submit" class="btn btn-dark">Lọc</button>
        </form>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Thời gian</th>
                    <th class="px-4 py-3">Cổng</th>
                    <th class="px-4 py-3">Chiều</th>
                    <th class="px-4 py-3">Loại</th>
                    <th class="px-4 py-3">Mã giao dịch</th>
                    <th class="px-4 py-3">Đơn nạp</th>
                    <th class="px-4 py-3">Chữ ký</th>
                    <th class="px-4 py-3">Mã</th>
                    <th class="px-4 py-3">Kết quả xử lý</th>
                    <th class="px-4 py-3">IP</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($logs as $log)
                    <tr class="{{ $log->signature_valid || $log->isOutgoing() ? '' : 'bg-red-50/50' }}">
                        <td class="px-4 py-3 text-gray-400">{{ $log->id }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs {{ $log->gateway === 'vnpay' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $log->gatewayLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs whitespace-nowrap {{ $log->isOutgoing() ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $log->directionLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->eventLabel() }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $log->transaction_id ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($log->deposit)
                                <span class="text-gray-700">#{{ $log->deposit_id }}</span>
                                <x-deposit-status :status="$log->deposit->status" />
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($log->isOutgoing())
                                <span class="text-gray-400">—</span>
                            @elseif ($log->signature_valid)
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Hợp lệ</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">Sai chữ ký</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $log->response_code ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-[260px] truncate" title="{{ $log->message }}">{{ $log->message ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $log->ip ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.webhook-logs.show', $log) }}" class="btn-xs btn-xs-primary">Chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="px-4 py-6 text-center text-gray-400">Chưa có log webhook nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</x-admin-layout>
