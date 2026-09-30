<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GameApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Quản lý người dùng: xem danh sách, xem hồ sơ + lịch sử nạp, sửa thông tin, khoá/mở khoá.
 * Không có chức năng xoá — khoá tài khoản là thao tác an toàn, đảo ngược được.
 */
class UserController extends Controller
{
    /**
     * Gợi ý user cho ô "Tài khoản" ở form nạp tay — tìm theo username/tên/email/SĐT,
     * ưu tiên khớp đầu chuỗi username.
     */
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $escaped = addcslashes($term, '%_\\');
        $like    = '%' . $escaped . '%';

        $users = User::query()
            ->where('role', 'user')
            ->where(fn ($q) => $q->where('username', 'like', $like)
                ->orWhere('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like))
            ->orderByRaw('CASE WHEN username = ? THEN 0 WHEN username LIKE ? THEN 1 ELSE 2 END', [$term, $escaped . '%'])
            ->orderBy('username')
            ->limit(10)
            ->get(['id', 'username', 'name', 'email', 'phone', 'xu_balance', 'status']);

        return response()->json($users);
    }

    /**
     * Danh sách user, tìm theo username/tên/email/SĐT, lọc theo trạng thái + TK game.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->where('role', 'user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%' . $request->string('q') . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('username', 'like', $keyword)
                        ->orWhere('name', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhere('phone', 'like', $keyword);
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->input('game') === 'none', fn ($q) => $q->whereNull('game_synced_at'))
            ->when($request->input('game') === 'ok',   fn ($q) => $q->whereNotNull('game_synced_at'))
            ->orderByDesc('id') // người dùng mới nhất lên đầu
            ->paginate(20)
            ->withQueryString();

        $gameStats = [
            'total' => User::query()->where('role', 'user')->count(),
            'none'  => User::query()->where('role', 'user')->whereNull('game_synced_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'gameStats'));
    }

    /** Mỗi lần bấm "Check khoản Ingame" xử lý tối đa bấy nhiêu user (mỗi request API ~10s timeout). */
    private const CHECK_GAME_BATCH = 50;

    /**
     * "Check khoản Ingame": hỏi checkuser.php cho các user chưa đánh dấu
     * game_synced_at (theo bộ lọc đang xem). Có trong game → đánh dấu; ngoài ra
     * KHÔNG thao tác gì thêm (không tạo, không đổi mật khẩu).
     */
    public function checkGame(Request $request, GameApiService $gameApi): RedirectResponse
    {
        $users = User::query()
            ->where('role', 'user')
            ->whereNull('game_synced_at')
            ->whereNotNull('username')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%' . $request->string('q') . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('username', 'like', $keyword)
                        ->orWhere('name', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhere('phone', 'like', $keyword);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderBy('id')
            ->limit(self::CHECK_GAME_BATCH)
            ->get();

        if ($users->isEmpty()) {
            return back()->with('status', 'Không có user nào cần kiểm tra (tất cả đã được đánh dấu có tài khoản game).');
        }

        $synced = $missing = $failed = 0;
        $missingNames = [];
        foreach ($users as $user) {
            $r = $gameApi->checkExists($user->username);
            if (! $r['ok']) {
                $failed++;
                Log::warning("checkGame: không hỏi được máy chủ game cho user #{$user->id} ({$user->username}): {$r['message']}");
                continue;
            }
            if ($r['exists']) {
                $user->markGameSynced();
                $synced++;
            } else {
                $missing++;
                $missingNames[] = $user->username;
            }
        }

        $msg = "Đã kiểm tra {$users->count()} user: {$synced} có tài khoản game (đã đánh dấu), {$missing} chưa có";
        if ($missingNames !== []) {
            $msg .= ' (' . implode(', ', array_slice($missingNames, 0, 10)) . (count($missingNames) > 10 ? ', …' : '') . ')';
        }
        if ($failed > 0) {
            $msg .= ", {$failed} lỗi kết nối (xem log)";
        }
        $msg .= '.';
        if ($users->count() === self::CHECK_GAME_BATCH) {
            $msg .= ' Còn user chưa kiểm tra — bấm lại để tiếp tục.';
        }

        return back()->with($failed > 0 && $synced === 0 ? 'error' : 'status', $msg);
    }

    /**
     * Form tạo tài khoản người dùng từ admin.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Tạo tài khoản mới — cùng bộ thông tin như form đăng ký ngoài frontend,
     * nhưng KHÔNG cần xác thực OTP, không áp giới hạn số tài khoản trên
     * mỗi số điện thoại / email, email và SĐT đều không bắt buộc và SĐT
     * không kiểm tra định dạng (admin là nguồn tin cậy).
     *
     * Tạo tài khoản game TRƯỚC, thất bại thì không tạo user web.
     */
    public function store(Request $request, GameApiService $gameApi): RedirectResponse
    {
        // Username luôn lưu chữ thường (game không phân biệt hoa/thường).
        $request->merge(['username' => strtolower(trim((string) $request->input('username')))]);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'birthday' => ['required', 'date', 'before_or_equal:' . Carbon::now()->subYears(18)->toDateString()],
            'phone'    => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'string', 'email', 'max:255'],
            'username' => ['required', 'string', 'min:8', 'max:16', 'regex:/^[a-zA-Z0-9]+$/', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'max:32'],
            'gender'   => ['required', 'integer', 'in:1,2'],
            'address'  => ['nullable', 'string', 'max:255'],
        ], [
            'name.required'            => 'Vui lòng nhập họ tên.',
            'birthday.required'        => 'Vui lòng nhập ngày sinh.',
            'birthday.before_or_equal' => 'Người chơi đăng ký phải đủ 18 tuổi trở lên.',
            'phone.max'                => 'Số điện thoại tối đa 20 ký tự.',
            'email.email'              => 'Email không đúng định dạng.',
            'username.required'        => 'Vui lòng nhập tên đăng nhập.',
            'username.min'             => 'Tên đăng nhập từ 8-16 ký tự.',
            'username.max'             => 'Tên đăng nhập từ 8-16 ký tự.',
            'username.regex'           => 'Tên đăng nhập chỉ gồm chữ cái và số, không dùng ký tự đặc biệt.',
            'username.unique'          => 'Tên đăng nhập đã tồn tại.',
            'password.required'        => 'Vui lòng nhập mật khẩu.',
            'password.min'             => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.max'             => 'Mật khẩu tối đa 32 ký tự.',
            'gender.required'          => 'Vui lòng chọn giới tính.',
            'gender.in'                => 'Vui lòng chọn giới tính.',
        ]);

        // 1. Tạo tài khoản game TRƯỚC — thất bại thì không tạo user web.
        $result = $gameApi->createAccount($validated['username'], $validated['password']);
        if (! $result['ok']) {
            return back()->withInput()->with('error', 'Tạo tài khoản thất bại: ' . $result['message']);
        }

        // 2. Game đã tạo xong thì mới ghi user xuống web.
        try {
            $user = User::query()->create([
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'] ?? null,
                'phone'    => $validated['phone'] ?? null,
                'birthday' => $validated['birthday'],
                'gender'   => $validated['gender'],
                'address'  => $validated['address'] ?? null,
                'password' => Hash::make($validated['password']),
                'plain_password' => $validated['password'],
            ]);
            $user->forceFill(['game_synced_at' => now()])->save(); // register.php đã OK ở bước 1
        } catch (\Throwable $e) {
            Log::error('admin store user: đã tạo trong game nhưng lỗi khi lưu web — ' . $e->getMessage());

            return back()->withInput()->with('error', 'Tài khoản game đã được tạo nhưng chưa lưu được trên web, vui lòng liên hệ quản trị.');
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "Đã tạo tài khoản {$user->username} (cả web và trong game).");
    }

    /**
     * Hồ sơ user + lịch sử nạp tiền.
     */
    public function show(User $user): View
    {
        $deposits = $user->deposits()
            ->with('processedBy')
            ->latest()
            ->paginate(20);

        return view('admin.users.show', compact('user', 'deposits'));
    }

    /**
     * Form sửa thông tin cá nhân của user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Admin cập nhật thông tin cá nhân cho user (họ tên, ngày sinh, SĐT, email,
     * giới tính, địa chỉ). Không đổi username (gắn với tài khoản game) và không
     * đổi mật khẩu ở đây (có form riêng, phải đồng bộ với game).
     *
     * Giống lúc tạo: email/SĐT không bắt buộc, SĐT không kiểm tra định dạng và
     * không giới hạn số tài khoản trên mỗi SĐT/email. Thay đổi được UserObserver
     * ghi vào lịch sử.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'birthday' => ['nullable', 'date', 'before_or_equal:' . Carbon::now()->subYears(18)->toDateString()],
            'phone'    => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'string', 'email', 'max:255'],
            'gender'   => ['nullable', 'integer', 'in:1,2'],
            'address'  => ['nullable', 'string', 'max:255'],
        ], [
            'name.required'            => 'Vui lòng nhập họ tên.',
            'birthday.before_or_equal' => 'Người chơi phải đủ 18 tuổi trở lên.',
            'phone.max'                => 'Số điện thoại tối đa 20 ký tự.',
            'email.email'              => 'Email không đúng định dạng.',
            'gender.in'                => 'Giới tính không hợp lệ.',
        ]);

        $user->fill([
            'name'     => $validated['name'],
            'birthday' => $validated['birthday'] ?? null,
            'phone'    => $validated['phone'] ?? null,
            'email'    => $validated['email'] ?? null,
            'gender'   => $validated['gender'] ?? null,
            'address'  => $validated['address'] ?? null,
        ]);

        if (! $user->isDirty()) {
            return redirect()->route('admin.users.show', $user)->with('status', 'Không có thay đổi nào.');
        }

        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "Đã cập nhật thông tin tài khoản {$user->username}.");
    }

    /** Lịch sử thay đổi thông tin của user (ghi bởi UserObserver). */
    public function changes(User $user): View
    {
        $logs = $user->changeLogs()->paginate(30);

        return view('admin.users.changes', compact('user', 'logs'));
    }

    /**
     * Khoá / mở khoá tài khoản. Không cho tự khoá chính mình.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể khoá tài khoản của chính bạn.');
        }

        // status ngoài Fillable, gán trực tiếp thuộc tính.
        $user->status = $user->status === 'active' ? 'locked' : 'active';
        $user->save();

        $label = $user->status === 'locked' ? 'khoá' : 'mở khoá';

        return back()->with('status', "Đã {$label} tài khoản " . ($user->username ?? $user->email ?? "#{$user->id}") . '.');
    }

    /**
     * Admin đặt lại mật khẩu cho user — đổi cả trong game lẫn trên web.
     *
     * Khác trang đổi mật khẩu phía người chơi: admin không phải nhập mật khẩu cũ.
     * Chỉ báo thành công khi CẢ hai bên cùng đổi xong.
     */
    public function changePassword(Request $request, User $user, GameApiService $gameApi): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:32'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min'      => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.max'      => 'Mật khẩu tối đa 32 ký tự.',
        ]);

        if (empty($user->username)) {
            return back()->with('error', 'Tài khoản này chưa có tên đăng nhập game, không thể đổi mật khẩu.');
        }

        // 1. Đổi trong game TRƯỚC — thất bại thì giữ nguyên mật khẩu web,
        //    tránh cảnh web một mật khẩu còn game một mật khẩu khác.
        //    User đăng ký trước khi có API game có thể chưa có tài khoản trong game:
        //    kiểm tra trước, chưa có thì TẠO MỚI bằng mật khẩu này thay vì đổi.
        $check = $gameApi->checkExists($user->username);
        if (! $check['ok']) {
            return back()->with('error', 'Không kiểm tra được tài khoản game: ' . $check['message']);
        }

        $createdInGame = false;
        if ($check['exists']) {
            $result = $gameApi->changePassword($user->username, $request->input('password'));
        } else {
            $result = $gameApi->createAccount($user->username, $request->input('password'));
            $createdInGame = $result['ok'];
        }

        if (! $result['ok']) {
            return back()->with('error', 'Đổi mật khẩu thất bại: ' . $result['message']);
        }

        // 2. Game đã đổi xong thì mới ghi mật khẩu mới xuống web.
        try {
            $user->forceFill([
                'password'       => Hash::make($request->input('password')),
                'plain_password' => $request->input('password'),
                'game_synced_at' => $user->game_synced_at ?? now(),
            ])->save();
        } catch (\Throwable $e) {
            Log::error('admin changePassword: đã đổi trong game nhưng lỗi khi lưu web — ' . $e->getMessage());

            return back()->with('error', 'Mật khẩu trong game đã đổi nhưng chưa lưu được trên web, vui lòng thử lại.');
        }

        return back()->with('status', $createdInGame
            ? "Tài khoản {$user->username} chưa có trong game — đã tạo mới trong game và lưu mật khẩu trên web."
            : "Đã đổi mật khẩu tài khoản {$user->username} (cả web và trong game).");
    }

    /**
     * Tạo tài khoản game cho user đã có trên web nhưng chưa có trong game
     * (đăng ký trước khi có API game). Dùng mật khẩu rõ đã lưu (plain_password)
     * nên mật khẩu game = mật khẩu web, user không cần làm gì thêm.
     */
    public function createGameAccount(User $user, GameApiService $gameApi): RedirectResponse
    {
        if (empty($user->username)) {
            return back()->with('error', 'Tài khoản này chưa có tên đăng nhập game.');
        }

        if ((string) $user->plain_password === '') {
            return back()->with('error', 'Chưa lưu mật khẩu rõ của tài khoản này. Hãy dùng "Đổi mật khẩu" bên dưới: thao tác đó sẽ tạo/đồng bộ trong game và lưu lại mật khẩu.');
        }

        $check = $gameApi->checkExists($user->username);
        if (! $check['ok']) {
            return back()->with('error', 'Không kiểm tra được tài khoản game: ' . $check['message']);
        }
        if ($check['exists']) {
            $user->markGameSynced();

            return back()->with('status', "Tài khoản {$user->username} đã có sẵn trong game — đã đánh dấu, không tạo lại.");
        }

        $result = $gameApi->createAccount($user->username, $user->plain_password);
        if (! $result['ok']) {
            return back()->with('error', 'Tạo tài khoản game thất bại: ' . $result['message']);
        }

        $user->markGameSynced();
        Log::info("admin createGameAccount: đã tạo tài khoản game cho user #{$user->id} ({$user->username})");

        return back()->with('status', "Đã tạo tài khoản game {$user->username} với mật khẩu đang lưu trên web.");
    }
}
