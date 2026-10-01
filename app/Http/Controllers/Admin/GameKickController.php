<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameKickLog;
use App\Services\GameKickService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameKickController extends Controller
{
    public function __construct(
        protected GameKickService $kickService
    ) {}

    /**
     * Giao diện chức năng Kick Người Chơi và danh sách lịch sử.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total'   => GameKickLog::count(),
            'success' => GameKickLog::where('code', '1')->count(),
            'pending' => GameKickLog::where('code', '0')->count(),
            'failed'  => GameKickLog::where('code', '2')->count(),
            'today'   => GameKickLog::whereDate('created_at', today())->count(),
        ];

        $logs = GameKickLog::query()
            ->with('admin')
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->string('type'));
            })
            ->when($request->filled('code'), function ($q) use ($request) {
                $q->where('code', $request->string('code'));
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->string('q');
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('role_name', 'like', "%{$search}%")
                        ->orWhere('target', 'like', "%{$search}%")
                        ->orWhere('by', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date('to'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.game-kicks.index', compact('stats', 'logs'));
    }

    /**
     * Gửi yêu cầu kick người chơi (Hỗ trợ 1 người hoặc nhiều người phân tách bằng dấu phẩy).
     */
    public function kick(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'type'   => ['required', 'in:account,role'],
            'name'   => ['required', 'string', 'max:2000'],
            'reason' => ['nullable', 'string', 'max:128'],
        ], [
            'name.required' => 'Vui lòng nhập tên tài khoản hoặc tên nhân vật.',
            'name.max'      => 'Danh sách tên quá dài (tối đa 2000 ký tự).',
            'reason.max'    => 'Lý do không được vượt quá 128 ký tự.',
        ]);

        // Tách chuỗi theo dấu phẩy, dấu chấm phẩy hoặc xuống dòng
        $rawNames = preg_split('/[,;\n\r]+/', $validated['name']);
        $names = array_values(array_unique(array_filter(array_map('trim', $rawNames), fn($v) => $v !== '')));

        if (empty($names)) {
            $msg = 'Vui lòng nhập ít nhất một tên tài khoản hoặc nhân vật hợp lệ.';
            return ($request->wantsJson() || $request->ajax())
                ? response()->json(['ok' => false, 'code' => '2', 'msg' => $msg])
                : back()->with('error', $msg);
        }

        $admin = auth('admin')->user();
        $by = $admin->username ?? $admin->name ?? 'admin';
        $adminId = $admin->id ?? null;

        // Trường hợp 1: Chỉ có 1 tài khoản -> Thực hiện đồng bộ trực tiếp (~2s) có kết quả ngay
        if (count($names) === 1) {
            $result = $this->kickService->kick(
                type: $validated['type'],
                name: $names[0],
                by: $by,
                reason: $validated['reason'] ?? '',
                adminId: $adminId
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($result);
            }

            if ($result['ok']) {
                return back()->with('status', $result['msg'] ?: 'Đã kick người chơi thành công!');
            }

            if ($result['code'] === '0') {
                return back()->with('status', 'Yêu cầu đang được GameServer xử lý (Pending). Hãy kiểm tra lại sau vài giây.');
            }

            return back()->with('error', $result['msg'] ?: 'Không thể kick người chơi. Vui lòng kiểm tra lại.');
        }

        // Trường hợp 2: Có nhiều tài khoản (phân cách bằng dấu phẩy) -> Đưa vào Queue Job tự động giãn cách 2s/acc
        $logIds = [];
        foreach ($names as $singleName) {
            $kickLog = GameKickLog::create([
                'type'     => $validated['type'],
                'name'     => mb_substr($singleName, 0, 96),
                'by'       => $by,
                'admin_id' => $adminId,
                'reason'   => $validated['reason'] ?? null,
                'nonce'    => bin2hex(random_bytes(8)),
                'code'     => '0',
                'result'   => 'pending',
                'msg'      => 'Đang trong hàng đợi xử lý ngầm (Queue)...',
            ]);
            $logIds[] = $kickLog->id;
        }

        \App\Jobs\ProcessGameKickBatch::dispatch($logIds);

        $batchMsg = "Đã đưa " . count($names) . " tài khoản vào hàng đợi (Queue). Hệ thống đang tự động kick ngầm lần lượt giãn cách 2s/acc để chống rate limit.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'       => true,
                'is_batch' => true,
                'count'    => count($names),
                'names'    => $names,
                'code'     => '0',
                'result'   => 'queued',
                'msg'      => $batchMsg,
            ]);
        }

        return back()->with('status', $batchMsg);
    }

    /**
     * Kiểm tra lại trạng thái lệnh kick đang pending.
     */
    public function checkStatus(Request $request, GameKickLog $kickLog): JsonResponse|RedirectResponse
    {
        $result = $this->kickService->checkStatus($kickLog);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['ok']) {
            return back()->with('status', 'Kết quả: ' . $result['msg']);
        }

        return back()->with($result['code'] === '0' ? 'status' : 'error', $result['msg']);
    }
}


