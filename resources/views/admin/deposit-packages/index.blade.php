<x-admin-layout>
    <x-slot name="header">Gói nạp</x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
            <p class="font-medium mb-1">Chưa lưu được, vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        @php
            $pkgRows = old('packages', $depositPackages);
        @endphp
        <form method="POST" action="{{ route('admin.deposit-packages.update') }}">
            @method('PUT')
            @csrf

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
                    <h2 class="font-semibold text-gray-800 text-base">Gói nạp VNPay</h2>
                    <button type="button" id="pkg-add"
                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md">
                        + Thêm gói
                    </button>
                </div>

                <p class="text-xs text-gray-500 mb-4">
                    Các gói hiện ở trang <strong>Nạp Thẻ</strong> và cũng là các mức đổi trên trang <strong>Đổi KNB</strong>
                    ({{ $pointPerKnb }} KPoint = 1 KNB, nên KPoint phải chia hết cho {{ $pointPerKnb }}).
                    Gói được tự sắp xếp theo số tiền khi lưu. Frontend cập nhật trong khoảng 1 phút.
                </p>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 border-b">
                            <th class="py-2 pr-3 w-10">#</th>
                            <th class="py-2 pr-3">Số tiền (VND)</th>
                            <th class="py-2 pr-3">KPoint nhận</th>
                            <th class="py-2 pr-3 text-gray-400 font-normal">Tỉ lệ</th>
                            <th class="py-2 w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="pkg-rows">
                        @foreach ($pkgRows as $i => $pkg)
                            <tr class="pkg-row border-b last:border-0">
                                <td class="py-2 pr-3 text-gray-400 pkg-index">{{ $loop->iteration }}</td>
                                <td class="py-2 pr-3">
                                    <input type="number" min="10000" step="1000" required
                                           name="packages[{{ $i }}][amount]" value="{{ $pkg['amount'] ?? '' }}"
                                           class="pkg-amount w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </td>
                                <td class="py-2 pr-3">
                                    <input type="number" min="{{ $pointPerKnb }}" step="{{ $pointPerKnb }}" required
                                           name="packages[{{ $i }}][point]" value="{{ $pkg['point'] ?? '' }}"
                                           class="pkg-point w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </td>
                                <td class="py-2 pr-3 text-xs text-gray-400 pkg-rate whitespace-nowrap"></td>
                                <td class="py-2 text-right">
                                    <button type="button" class="pkg-remove text-red-600 hover:underline text-sm">Xoá</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                Lưu gói nạp
            </button>
        </form>
    </div>

    <script>
    (function () {
        const tbody = document.getElementById('pkg-rows');
        const addBtn = document.getElementById('pkg-add');
        if (!tbody || !addBtn) return;

        let nextIndex = tbody.querySelectorAll('.pkg-row').length;

        // Hiện tỉ lệ KPoint / 1.000đ để admin dễ soát lỗi gõ nhầm số 0.
        function refresh() {
            tbody.querySelectorAll('.pkg-row').forEach(function (row, i) {
                row.querySelector('.pkg-index').textContent = i + 1;
                const amount = parseInt(row.querySelector('.pkg-amount').value, 10);
                const point  = parseInt(row.querySelector('.pkg-point').value, 10);
                row.querySelector('.pkg-rate').textContent = amount > 0 && point > 0
                    ? (point * 1000 / amount).toLocaleString('vi-VN', { maximumFractionDigits: 2 }) + ' / 1.000đ'
                    : '';
            });
        }

        addBtn.addEventListener('click', function () {
            const tpl = tbody.querySelector('.pkg-row');
            const row = tpl.cloneNode(true);
            row.querySelectorAll('input').forEach(function (input) {
                input.value = '';
                input.name = input.name.replace(/packages\[\d+\]/, 'packages[' + nextIndex + ']');
            });
            nextIndex++;
            tbody.appendChild(row);
            refresh();
            row.querySelector('.pkg-amount').focus();
        });

        tbody.addEventListener('click', function (e) {
            if (!e.target.classList.contains('pkg-remove')) return;
            if (tbody.querySelectorAll('.pkg-row').length <= 1) {
                alert('Cần giữ lại ít nhất 1 gói nạp.');
                return;
            }
            e.target.closest('.pkg-row').remove();
            refresh();
        });

        tbody.addEventListener('input', refresh);
        refresh();
    })();
    </script>
</x-admin-layout>
