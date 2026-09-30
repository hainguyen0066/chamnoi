<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Cấu hình các gói nạp VNPay của trang /nap-the (volam-laravel), lưu ở
 * setting `deposit_packages` dạng JSON [{amount, point}, ...].
 */
class DepositPackageController extends Controller
{
    /**
     * Gói nạp mặc định, khớp Deposit::PACKAGES bên volam-laravel. Chỉ dùng để
     * điền sẵn form khi setting deposit_packages chưa có.
     */
    private const DEFAULT_DEPOSIT_PACKAGES = [
        ['amount' => 50_000, 'point' => 450],
        ['amount' => 100_000, 'point' => 900],
        ['amount' => 200_000, 'point' => 1_800],
        ['amount' => 300_000, 'point' => 2_700],
        ['amount' => 500_000, 'point' => 4_500],
        ['amount' => 1_000_000, 'point' => 9_000],
        ['amount' => 2_000_000, 'point' => 18_000],
        ['amount' => 3_000_000, 'point' => 27_000],
        ['amount' => 5_000_000, 'point' => 45_000],
        ['amount' => 10_000_000, 'point' => 90_000],
    ];

    /** Đổi KNB dùng mức Point của gói nạp: 10 point = 1 KNB, nên point phải chia hết cho 10. */
    private const POINT_PER_KNB = 10;

    public function index(): View
    {
        $packages = json_decode((string) Setting::query()->where('key', 'deposit_packages')->value('value'), true);

        return view('admin.deposit-packages.index', [
            'depositPackages' => is_array($packages) && $packages !== [] ? $packages : self::DEFAULT_DEPOSIT_PACKAGES,
            'pointPerKnb'     => self::POINT_PER_KNB,
        ]);
    }

    /**
     * Lưu danh sách gói nạp VNPay hiển thị ở trang /nap-the (volam-laravel) —
     * đọc qua Deposit::packages(). Frontend cache settings 60 giây nên thay đổi
     * có hiệu lực trong khoảng 1 phút. Giao dịch đang chờ không bị ảnh hưởng vì
     * số Point đã được chốt vào deposits.amount_received lúc tạo đơn.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'packages'          => ['required', 'array', 'min:1', 'max:30'],
            'packages.*.amount' => ['required', 'integer', 'min:10000', 'max:1000000000', 'distinct'],
            'packages.*.point'  => ['required', 'integer', 'min:' . self::POINT_PER_KNB, 'max:100000000', 'multiple_of:' . self::POINT_PER_KNB],
        ], [
            'packages.required'           => 'Cần ít nhất 1 gói nạp.',
            'packages.min'                => 'Cần ít nhất 1 gói nạp.',
            'packages.*.amount.required'  => 'Vui lòng nhập số tiền cho mọi gói.',
            'packages.*.amount.min'       => 'Số tiền mỗi gói tối thiểu 10.000 VND.',
            'packages.*.amount.distinct'  => 'Có 2 gói trùng số tiền.',
            'packages.*.point.required'   => 'Vui lòng nhập số KPoint cho mọi gói.',
            'packages.*.point.min'        => 'KPoint mỗi gói tối thiểu ' . self::POINT_PER_KNB . '.',
            'packages.*.point.multiple_of' => 'KPoint phải chia hết cho ' . self::POINT_PER_KNB . ' (để đổi KNB không bị lẻ).',
        ]);

        $packages = collect($request->input('packages'))
            ->map(fn ($p) => ['amount' => (int) $p['amount'], 'point' => (int) $p['point']])
            ->sortBy('amount')
            ->values()
            ->all();

        Setting::query()->updateOrCreate(
            ['key' => 'deposit_packages'],
            ['value' => json_encode($packages), 'group' => 'payment']
        );
        Cache::forget(Setting::CACHE_KEY);

        return redirect()->route('admin.deposit-packages.index')
            ->with('status', 'Đã lưu ' . count($packages) . ' gói nạp. Trang nạp thẻ cập nhật trong khoảng 1 phút.');
    }
}

