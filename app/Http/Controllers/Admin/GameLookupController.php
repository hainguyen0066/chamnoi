<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLookupLog;
use App\Services\GameLookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameLookupController extends Controller
{
    public function __construct(
        protected GameLookupService $lookupService
    ) {}

    /**
     * Hiển thị trang tra cứu Role <-> Account.
     */
    public function index(Request $request): View
    {
        $admin = auth('admin')->user();

        $stats = [
            'total'     => GameLookupLog::count(),
            'success'   => GameLookupLog::where('code', '1')->count(),
            'not_found' => GameLookupLog::where('result', 'not_found')->count(),
            'today'     => GameLookupLog::whereDate('created_at', today())->count(),
        ];

        $query = GameLookupLog::with('admin')->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('query_name', 'like', "%{$s}%")
                  ->orWhere('account', 'like', "%{$s}%");
            });
        }

        if ($request->filled('by')) {
            $query->where('by', $request->by);
        }

        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.game-lookups.index', [
            'stats' => $stats,
            'logs'  => $logs,
            'admin' => $admin,
        ]);
    }

    /**
     * Xử lý yêu cầu tra cứu (đơn lẻ hoặc hàng loạt).
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'by'    => 'required|in:role,account',
            'names' => 'required|string',
        ]);

        $admin = auth('admin')->user();
        $adminName = $admin?->name ?: $admin?->email ?: 'admin';
        $adminId = $admin?->id;

        $rawNames = $validated['names'];
        // Tách theo dòng hoặc dấu phẩy
        $lines = preg_split('/[\r\n,]+/', $rawNames);
        $names = array_values(array_unique(array_filter(array_map('trim', $lines))));

        if (empty($names)) {
            return response()->json([
                'ok'      => false,
                'code'    => '2',
                'result'  => 'bad_request',
                'msg'     => 'Vui lòng nhập tên nhân vật hoặc tài khoản cần tra cứu.',
                'results' => [],
            ], 422);
        }

        // Nếu chỉ có 1 mục cần tra
        if (count($names) === 1) {
            $single = $this->lookupService->lookup(
                $validated['by'],
                $names[0],
                $adminName,
                $adminId,
                true
            );

            return response()->json([
                'ok'      => $single['ok'],
                'isBatch' => false,
                'data'    => $single,
                'results' => [$single],
                'msg'     => $single['msg'],
            ]);
        }

        // Giới hạn tra cứu tối đa 50 tên một lần để bảo vệ hiệu năng
        if (count($names) > 50) {
            $names = array_slice($names, 0, 50);
        }

        $batchResults = $this->lookupService->lookupBatch(
            $validated['by'],
            $names,
            $adminName,
            $adminId
        );

        $successCount = count(array_filter($batchResults, fn ($item) => $item['ok']));

        return response()->json([
            'ok'      => true,
            'isBatch' => true,
            'total'   => count($batchResults),
            'success' => $successCount,
            'results' => $batchResults,
            'msg'     => "Đã tra cứu xong " . count($batchResults) . " mục. Tìm thấy {$successCount} kết quả.",
        ]);
    }
}
