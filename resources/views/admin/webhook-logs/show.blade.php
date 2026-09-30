<x-admin-layout>
    <x-slot name="header">Chi tiết Webhook #{{ $log->id }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.webhook-logs.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Quay lại danh sách</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Thông tin chung --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-semibold text-gray-800 text-base mb-4">Thông tin webhook</h2>
            <table class="text-sm w-full">
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="py-2 pr-4 text-gray-400 w-40">Thời gian</td><td class="py-2">{{ $log->created_at->format('d/m/Y H:i:s') }}</td></tr>
                    <tr><td class="py-2 pr-4 text-gray-400">Cổng</td><td class="py-2">{{ $log->gatewayLabel() }}</td></tr>
                    <tr><td class="py-2 pr-4 text-gray-400">Chiều</td><td class="py-2">{{ $log->directionLabel() }}</td></tr>
                    <tr><td class="py-2 pr-4 text-gray-400">Loại</td><td class="py-2">{{ $log->eventLabel() }}</td></tr>
                    <tr><td class="py-2 pr-4 text-gray-400">Mã giao dịch</td><td class="py-2 font-mono text-xs">{{ $log->transaction_id ?? '—' }}</td></tr>
                    <tr>
                        <td class="py-2 pr-4 text-gray-400">Chữ ký</td>
                        <td class="py-2">
                            @if ($log->isOutgoing())
                                <span class="text-gray-400 text-xs">Không áp dụng (request mình gửi đi)</span>
                            @elseif ($log->signature_valid)
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Hợp lệ</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">Sai chữ ký</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="py-2 pr-4 text-gray-400">Mã phản hồi</td><td class="py-2 font-mono text-xs">{{ $log->response_code ?? '—' }}</td></tr>
                    <tr><td class="py-2 pr-4 text-gray-400">Kết quả xử lý</td><td class="py-2">{{ $log->message ?? '—' }}</td></tr>
                    <tr><td class="py-2 pr-4 text-gray-400">IP nguồn</td><td class="py-2 font-mono text-xs">{{ $log->ip ?? '—' }}</td></tr>
                </tbody>
            </table>
        </div>

        {{-- Đơn nạp liên quan --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-semibold text-gray-800 text-base mb-4">Đơn nạp liên quan</h2>
            @if ($log->deposit)
                <table class="text-sm w-full">
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="py-2 pr-4 text-gray-400 w-40">Mã đơn</td><td class="py-2">#{{ $log->deposit->id }}</td></tr>
                        <tr><td class="py-2 pr-4 text-gray-400">Tài khoản</td><td class="py-2">{{ $log->deposit->account }}</td></tr>
                        <tr><td class="py-2 pr-4 text-gray-400">Số tiền</td><td class="py-2">{{ number_format($log->deposit->amount) }} đ</td></tr>
                        <tr><td class="py-2 pr-4 text-gray-400">KPoint nhận</td><td class="py-2 font-medium">{{ number_format($log->deposit->amount_received) }}</td></tr>
                        <tr>
                            <td class="py-2 pr-4 text-gray-400">Trạng thái</td>
                            <td class="py-2"><x-deposit-status :status="$log->deposit->status" /></td>
                        </tr>
                        <tr><td class="py-2 pr-4 text-gray-400">Tạo lúc</td><td class="py-2">{{ $log->deposit->created_at->format('d/m/Y H:i:s') }}</td></tr>
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400">Không tìm thấy đơn nạp tương ứng với webhook này.</p>
            @endif
        </div>
    </div>

    {{-- Payload --}}
    <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
        <h2 class="font-semibold text-gray-800 text-base mb-4">
            {{ $log->isOutgoing() ? 'Tham số mình gửi sang cổng' : 'Payload (dữ liệu gateway gửi tới)' }}
        </h2>
        @php $payload = $log->payloadArray(); @endphp
        @if (count($payload))
            <div class="overflow-x-auto">
                <table class="text-sm w-full">
                    <thead class="bg-gray-50 text-gray-500 text-left">
                        <tr>
                            <th class="px-3 py-2 w-64">Tham số</th>
                            <th class="px-3 py-2">Giá trị</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($payload as $key => $value)
                            <tr>
                                <td class="px-3 py-2 font-mono text-xs text-gray-500">{{ $key }}</td>
                                <td class="px-3 py-2 font-mono text-xs break-all">{{ is_scalar($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-400">Không có dữ liệu payload.</p>
        @endif
    </div>

    {{-- Response mình trả về / URL đã gửi đi --}}
    <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
        <h2 class="font-semibold text-gray-800 text-base mb-4">
            {{ $log->isOutgoing() ? 'URL đã gửi sang cổng' : 'Response mình trả về cho cổng' }}
        </h2>
        @if (filled($log->response_body))
            <pre class="bg-gray-50 rounded-md p-4 text-xs font-mono break-all whitespace-pre-wrap">{{ $log->response_body }}</pre>
        @else
            <p class="text-sm text-gray-400">Không có dữ liệu (log ghi trước khi tính năng này được thêm).</p>
        @endif
    </div>
</x-admin-layout>
