<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

/**
 * Trang tổng quan admin: các số liệu nhanh.
 */
class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::query()->where('role', 'user')->count(),
            'totalPosts' => Post::query()->count(),
            'todayRevenue' => Deposit::query()
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'todayDeposits' => Deposit::query()
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->count(),
            'latestDeposits' => Deposit::query()
                ->with('user')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
