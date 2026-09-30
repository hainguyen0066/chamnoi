<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnbExchange;
use App\Services\GameApiService;
use App\Services\KnbExchangeSettlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Quản lý giao dịch đổi K Point → KNB (bảng knb_exchanges, tạo từ trang
 * /tai-khoan/doi-knb bên volam-laravel). Chỉ xem + gọi lại giao dịch pending;
 * không cho sửa tay trạng thái vì dễ làm lệch Point/KNB.
 */
class KnbExchangeController extends Controller
{
    public function index(Request $request): View
    {
        $query = KnbExchange::query()
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($w) => $w->where('username', 'like', $term)->orWhere('txnid', 'like', $term));
            });

        // Thống kê theo đúng bộ lọc hiện tại (không phân trang).
        $stats = (clone $query)
            ->selectRaw('status, COUNT(*) as total, SUM(point_amount) as points, SUM(knb_amount) as knb')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $exchanges = $query->with('user')->latest('id')->paginate(20)->withQueryString();

        return view('admin.knb-exchanges.index', compact('exchanges', 'stats'));
    }

    public function show(KnbExchange $knbExchange): View
    {
        $knbExchange->load('user');

        return view('admin.knb-exchanges.show', ['exchange' => $knbExchange]);
    }

    /**
     * Gọi lại recharge.php cho 1 giao dịch pending — cùng logic với lệnh
     * `php artisan knb:retry-pending --id=` bên volam-laravel. An toàn khi bấm
     * nhiều lần: txnid không đổi nên server game không cộng KNB trùng.
     */
    public function retry(KnbExchange $knbExchange, GameApiService $gameApi, KnbExchangeSettlementService $settlement): RedirectResponse
    {
        if ($knbExchange->status !== 'pending' || ! $knbExchange->txnid) {
            return back()->with('error', 'Chỉ gọi lại được giao dịch đang chờ xử lý.');
        }

        $result  = $gameApi->rechargeKnb($knbExchange->username, $knbExchange->knb_amount, $knbExchange->txnid);
        $outcome = $settlement->settle($knbExchange, $result);

        return back()->with($outcome['success'] ? 'status' : 'error', "#{$knbExchange->id}: {$outcome['message']}");
    }
}
