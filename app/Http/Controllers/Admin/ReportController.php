<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Báo cáo doanh thu nạp theo ngày/tháng/khoảng thời gian tự chọn.
 * Chỉ tính giao dịch status=completed.
 */
class ReportController extends Controller
{
    /**
     * Trang báo cáo: preset hôm nay / 7 ngày / tháng này, hoặc khoảng tự chọn.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        // Mặc định: 30 ngày gần nhất.
        $from = $request->filled('from') ? $request->date('from')->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->filled('to') ? $request->date('to')->endOfDay() : now()->endOfDay();

        $base = Deposit::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to]);

        // Doanh thu theo ngày cho biểu đồ (line chart).
        $daily = (clone $base)
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total_vnd, SUM(amount_received) as total_xu, COUNT(*) as total_count')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Phân rã theo phương thức nạp cho bảng tổng hợp.
        $byMethod = (clone $base)
            ->selectRaw('method, SUM(amount) as total_vnd, SUM(amount_received) as total_xu, COUNT(*) as total_count')
            ->groupBy('method')
            ->orderByDesc('total_vnd')
            ->get();

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'daily' => $daily,
            'byMethod' => $byMethod,
            'totalVnd' => $daily->sum('total_vnd'),
            'totalXu' => $daily->sum('total_xu'),
            'totalCount' => $daily->sum('total_count'),
            'methodLabels' => Deposit::METHODS,
        ]);
    }
}
