<x-admin-layout>
    <x-slot name="header">Tổng quan</x-slot>

    {{-- Số liệu nhanh --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Người dùng</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Bài viết</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalPosts) }}</p>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Doanh thu hôm nay</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($todayRevenue) }} đ</p>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Lượt nạp hôm nay</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($todayDeposits) }}</p>
        </div>
    </div>

    {{-- Giao dịch gần nhất --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Giao dịch nạp gần nhất</h2>
            <a href="{{ route('admin.deposits.index') }}" class="text-sm text-indigo-600 hover:underline">Xem tất cả</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3">Thời gian</th>
                    <th class="px-5 py-3">Tài khoản</th>
                    <th class="px-5 py-3">Phương thức</th>
                    <th class="px-5 py-3 text-right">Số tiền</th>
                    <th class="px-5 py-3 text-right">KPoint</th>
                    <th class="px-5 py-3">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($latestDeposits as $deposit)
                    <tr>
                        <td class="px-5 py-3 text-gray-500">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3">{{ $deposit->account }}</td>
                        <td class="px-5 py-3">{{ \App\Models\Deposit::METHODS[$deposit->method] ?? $deposit->method }}</td>
                        <td class="px-5 py-3 text-right">{{ number_format($deposit->amount) }} đ</td>
                        <td class="px-5 py-3 text-right font-medium">{{ number_format($deposit->amount_received) }}</td>
                        <td class="px-5 py-3">
                            <x-deposit-status :status="$deposit->status" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-6 text-center text-gray-400">Chưa có giao dịch nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
