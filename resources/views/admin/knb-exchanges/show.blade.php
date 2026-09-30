<x-admin-layout>
    <x-slot name="header">Giao dịch đổi KPoint #{{ $exchange->id }}</x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.knb-exchanges.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Danh sách đổi KPoint</a>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm">
            <div><dt class="text-gray-500">Mã GD (txnid)</dt><dd class="font-mono">{{ $exchange->txnid ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Trạng thái</dt><dd><x-deposit-status :status="$exchange->status" /></dd></div>
            <div><dt class="text-gray-500">Tài khoản game</dt><dd>{{ $exchange->username }}</dd></div>
            <div>
                <dt class="text-gray-500">User web</dt>
                <dd>
                    @if ($exchange->user)
                        <a href="{{ route('admin.users.show', $exchange->user) }}" class="text-indigo-600 hover:underline">
                            {{ $exchange->user->email ?? $exchange->user->phone ?? ('#' . $exchange->user_id) }}
                        </a>
                        <span class="text-gray-400">· số dư hiện tại {{ number_format($exchange->user->xu_balance) }} KPoint</span>
                    @else
                        #{{ $exchange->user_id }}
                    @endif
                </dd>
            </div>
            <div><dt class="text-gray-500">KPoint trừ</dt><dd>{{ number_format($exchange->point_amount) }}</dd></div>
            <div><dt class="text-gray-500">KNB cộng</dt><dd class="font-medium">{{ number_format($exchange->knb_amount) }}</dd></div>
            <div><dt class="text-gray-500">Tạo lúc</dt><dd>{{ $exchange->created_at->format('d/m/Y H:i:s') }}</dd></div>
            <div><dt class="text-gray-500">Cập nhật lúc</dt><dd>{{ $exchange->updated_at->format('d/m/Y H:i:s') }}</dd></div>
        </dl>

        @if ($exchange->status === 'pending')
            <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                Kết quả lần gọi API trước không rõ ràng nên KPoint của người chơi đang được giữ.
                Bấm "Thử lại" để gọi lại với cùng mã GD — server game không cộng KNB trùng.
                <form method="POST" action="{{ route('admin.knb-exchanges.retry', $exchange) }}" class="mt-3"
                      onsubmit="return confirm('Gọi lại API nạp KNB cho giao dịch này?');">
                    @csrf
                    <button type="submit" class="btn btn-primary">Thử lại</button>
                </form>
            </div>
        @elseif ($exchange->status === 'failed')
            <p class="mt-6 text-sm text-gray-500">
                Giao dịch thất bại chắc chắn — {{ number_format($exchange->point_amount) }} KPoint đã được hoàn lại cho người chơi.
            </p>
        @endif
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="font-semibold text-gray-800 text-base mb-3">Phản hồi từ API game</h2>
        @if ($exchange->game_response)
            <pre class="bg-gray-50 rounded p-4 text-xs overflow-x-auto">{{ json_encode($exchange->game_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        @else
            <p class="text-sm text-gray-400">Chưa có phản hồi.</p>
        @endif
    </div>
</x-admin-layout>
