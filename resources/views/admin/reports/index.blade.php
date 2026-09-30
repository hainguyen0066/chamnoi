<x-admin-layout>
    <x-slot name="header">Báo cáo doanh thu</x-slot>

    {{-- Bộ chọn khoảng thời gian --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
        <span class="text-gray-400 text-sm">đến</span>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
        <button type="submit" class="btn btn-primary">Xem</button>

        <span class="mx-2 text-gray-300">|</span>
        <a href="{{ route('admin.reports.index', ['from' => today()->format('Y-m-d'), 'to' => today()->format('Y-m-d')]) }}"
           class="btn btn-secondary">Hôm nay</a>
        <a href="{{ route('admin.reports.index', ['from' => now()->subDays(6)->format('Y-m-d'), 'to' => today()->format('Y-m-d')]) }}"
           class="btn btn-secondary">7 ngày</a>
        <a href="{{ route('admin.reports.index', ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => today()->format('Y-m-d')]) }}"
           class="btn btn-secondary">Tháng này</a>
    </form>

    {{-- Tổng hợp --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Tổng doanh thu</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($totalVnd) }} đ</p>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Tổng KPoint đã cấp</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalXu) }}</p>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-5">
            <p class="text-sm text-gray-500">Số giao dịch</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalCount) }}</p>
        </div>
    </div>

    {{-- Biểu đồ theo ngày --}}
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">Doanh thu theo ngày</h2>
        <canvas id="revenue-chart" height="90"></canvas>
    </div>

    {{-- Phân rã theo phương thức --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-800">Theo phương thức nạp</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3">Phương thức</th>
                    <th class="px-5 py-3 text-right">Số giao dịch</th>
                    <th class="px-5 py-3 text-right">Doanh thu (VND)</th>
                    <th class="px-5 py-3 text-right">KPoint đã cấp</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($byMethod as $row)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $methodLabels[$row->method] ?? $row->method }}</td>
                        <td class="px-5 py-3 text-right">{{ number_format($row->total_count) }}</td>
                        <td class="px-5 py-3 text-right">{{ number_format($row->total_vnd) }} đ</td>
                        <td class="px-5 py-3 text-right">{{ number_format($row->total_xu) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Không có dữ liệu trong khoảng này.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
    <script>
        new Chart(document.getElementById('revenue-chart'), {
            type: 'line',
            data: {
                labels: @json($daily->pluck('day')),
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: @json($daily->pluck('total_vnd')),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    fill: true,
                    tension: 0.25
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => value.toLocaleString('vi-VN') + ' đ' }
                    }
                }
            }
        });
    </script>
</x-admin-layout>
