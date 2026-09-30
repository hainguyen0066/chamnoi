<x-admin-layout>
    <x-slot name="header">Nạp tiền cho user</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Form nạp tay (Momo / Ngân hàng / CTV) --}}
        <form method="POST" action="{{ route('admin.deposits.store') }}" class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Nạp thủ công</h2>

            <div class="space-y-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Loại hình nạp</label>
                    <select id="type" name="type" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @foreach (\App\Models\Deposit::TYPES as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', 'xu') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 mb-1">Phương thức nạp</label>
                    <select id="method" name="method" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="">Chọn phương thức nạp</option>
                        <option value="momo" @selected(old('method') === 'momo')>Momo</option>
                        <option value="bank_transfer" @selected(old('method') === 'bank_transfer')>Ngân hàng</option>
                        <option value="ctv" @selected(old('method') === 'ctv')>CTV</option>
                    </select>
                    @error('method') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="relative">
                    <label for="account" class="block text-sm font-medium text-gray-700 mb-1">Tài khoản</label>
                    <input type="text" id="account" name="account" value="{{ old('account', request('account')) }}" required
                           autocomplete="off"
                           placeholder="Gõ tên tài khoản, tên, email hoặc SĐT để tìm..."
                           class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    {{-- Gợi ý user — lấy từ admin.users.search --}}
                    <ul id="account-suggest"
                        class="hidden absolute z-20 left-0 right-0 mt-1 max-h-72 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg text-sm"></ul>
                    <p id="account-picked" class="hidden text-xs text-green-700 mt-1"></p>
                    @error('account') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', 0) }}" min="0" step="1000" required
                           class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="promotion_percent" class="block text-sm font-medium text-gray-700 mb-1">Tỉ lệ khuyến mãi</label>
                    <select id="promotion_percent" name="promotion_percent" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @foreach ($promotionPercents as $percent)
                            <option value="{{ $percent }}" @selected((int) old('promotion_percent', 0) === $percent)>
                                {{ $percent === 0 ? 'Mặc định khuyến mãi 0%' : "Khuyến mãi {$percent}%" }}
                            </option>
                        @endforeach
                    </select>
                    @error('promotion_percent') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="xu-preview" class="block text-sm font-medium text-gray-700 mb-1">Số KPoint nhận được</label>
                    {{-- Chỉ để XEM TRƯỚC: không có name nên không gửi lên server;
                         server luôn tự tính lại từ amount + promotion_percent. --}}
                    <input type="text" id="xu-preview" value="0" readonly
                           class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm text-sm font-semibold">
                    <p class="text-xs text-pink-500 mt-1">KPoint cộng vào ví web của user (dùng để đổi KNB)</p>
                </div>

                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Nội dung nạp (Momo, Paypal, Ck, vv):</label>
                    <input type="text" id="note" name="note" value="{{ old('note') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    @error('note') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @csrf
            <button type="submit" class="mt-6 btn btn-primary btn-block"
                    onclick="return confirm('Xác nhận nạp cho tài khoản này? KPoint sẽ được cộng ngay.');">
                Nạp tiền
            </button>
        </form>

        {{-- Công cụ tạo link thanh toán gateway (test sandbox) --}}
        <div class="space-y-4">
            <form method="POST" action="{{ route('admin.deposits.payment-link') }}" class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-semibold text-gray-800 mb-1">Tạo link thanh toán thử (gateway)</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Tạo giao dịch pending và link/QR thanh toán sandbox VNPay hoặc VNPT Pay.
                    KPoint chỉ được cộng khi cổng thanh toán callback xác nhận thành công.
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                        <input type="number" id="user_id" name="user_id" value="{{ old('user_id') }}" min="1" required
                               placeholder="ID của user (xem ở trang Người dùng)"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="gateway" class="block text-sm font-medium text-gray-700 mb-1">Cổng thanh toán</label>
                        <select id="gateway" name="gateway" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="vnpay">VNPay</option>
                            <option value="vnptpay">VNPT Pay</option>
                        </select>
                    </div>
                    <div>
                        <label for="gw-amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền (VND)</label>
                        <input type="number" id="gw-amount" name="amount" value="{{ old('amount', 50000) }}" min="10000" step="1000" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @csrf
                <button type="submit" class="mt-6 btn btn-dark btn-block">
                    Tạo link thanh toán
                </button>
            </form>

            @if (session('payment_url'))
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Link thanh toán</h3>
                    <a href="{{ session('payment_url') }}" target="_blank" rel="noopener"
                       class="block break-all text-sm text-indigo-600 hover:underline">
                        {{ session('payment_url') }}
                    </a>
                </div>
            @endif

            @if (session('payment_qr'))
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Dữ liệu QR (VNPT Pay)</h3>
                    <code class="block break-all text-xs text-gray-600">{{ session('payment_qr') }}</code>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Xem trước số xu nhận được — CÙNG công thức với server:
        // xu = floor( số tiền * (1 + khuyến mãi%) / {{ $vndPerXu }} )
        (function () {
            const amountEl = document.getElementById('amount');
            const percentEl = document.getElementById('promotion_percent');
            const previewEl = document.getElementById('xu-preview');

            function updatePreview() {
                const amount = parseInt(amountEl.value, 10) || 0;
                const percent = parseInt(percentEl.value, 10) || 0;
                const xu = Math.floor(amount * (1 + percent / 100) / {{ $vndPerXu }});
                previewEl.value = xu.toLocaleString('vi-VN');
            }

            amountEl.addEventListener('input', updatePreview);
            percentEl.addEventListener('change', updatePreview);
            updatePreview();
        })();

        // Gợi ý tài khoản: gõ >= 2 ký tự → gọi admin.users.search, chọn để điền username.
        (function () {
            const input  = document.getElementById('account');
            const list   = document.getElementById('account-suggest');
            const picked = document.getElementById('account-picked');
            const url    = @json(route('admin.users.search'));
            let timer = null, items = [], active = -1, seq = 0;

            function esc(s) {
                return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            }

            function close() { list.classList.add('hidden'); active = -1; }

            function render() {
                if (!items.length) {
                    list.innerHTML = '<li class="px-3 py-2 text-gray-400">Không tìm thấy user phù hợp</li>';
                } else {
                    list.innerHTML = items.map((u, i) => `
                        <li data-i="${i}" class="px-3 py-2 cursor-pointer ${i === active ? 'bg-indigo-50' : 'hover:bg-gray-50'}">
                            <div class="font-medium text-gray-800">${esc(u.username || '(chưa có username)')}
                                ${u.status && u.status !== 'active' ? '<span class="ml-1 text-xs text-red-500">(' + esc(u.status) + ')</span>' : ''}
                            </div>
                            <div class="text-xs text-gray-500">
                                #${u.id} · ${esc(u.name || '—')} · ${esc(u.phone || '')} ${u.email ? '· ' + esc(u.email) : ''}
                                · ${Number(u.xu_balance || 0).toLocaleString('vi-VN')} KPoint
                            </div>
                        </li>`).join('');
                }
                list.classList.remove('hidden');
            }

            function choose(u) {
                input.value = u.username || u.email || u.phone;
                picked.textContent = `✓ #${u.id} ${u.username || ''} — ${u.name || ''} · số dư ${Number(u.xu_balance || 0).toLocaleString('vi-VN')} KPoint`;
                picked.classList.remove('hidden');
                close();
            }

            input.addEventListener('input', function () {
                picked.classList.add('hidden');
                clearTimeout(timer);
                const q = input.value.trim();
                if (q.length < 2) { close(); return; }
                timer = setTimeout(async function () {
                    const my = ++seq;
                    try {
                        const res = await fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                        if (my !== seq) return; // bỏ kết quả cũ nếu đã gõ tiếp
                        items = res.ok ? await res.json() : [];
                        active = -1;
                        render();
                    } catch (e) { close(); }
                }, 250);
            });

            input.addEventListener('keydown', function (e) {
                if (list.classList.contains('hidden') || !items.length) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); active = (active + 1) % items.length; render(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); active = (active - 1 + items.length) % items.length; render(); }
                else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); choose(items[active]); }
                else if (e.key === 'Escape') { close(); }
            });

            // mousedown thay vì click để chạy trước sự kiện blur của input.
            list.addEventListener('mousedown', function (e) {
                const li = e.target.closest('li[data-i]');
                if (li) { e.preventDefault(); choose(items[+li.dataset.i]); }
            });
            input.addEventListener('blur', () => setTimeout(close, 150));
        })();
    </script>
</x-admin-layout>
