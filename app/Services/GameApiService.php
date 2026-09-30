<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gọi API máy chủ game (tạo tài khoản / đổi mật khẩu / kiểm tra tồn tại).
 *
 * File này giống hệt nhau ở volam-laravel và volam-laravel-admin — sửa một bên
 * thì copy sang bên kia.
 */
class GameApiService
{
    private string $baseUrl;
    private string $key;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            Setting::get('game_api_base_url') ?: config('services.game_api.base_url', 'http://api.tinhtrongthienha.vn/v2'),
            '/'
        );
        $this->key = (string) (Setting::get('game_api_key') ?: config('services.game_api.key', ''));
    }

    /**
     * Tạo tài khoản game mới.
     * sign = md5(KEY + tk)
     *
     * `exists` = true khi máy chủ báo tên đã tồn tại (ok = false) — caller dùng
     * để phân biệt "trùng tên" với lỗi thật.
     *
     * @return array{ok: bool, message: string, code: string, exists: bool}
     */
    public function createAccount(string $username, string $password): array
    {
        return $this->call(
            'register.php',
            ['tk' => $username, 'mk' => $password, 'sign' => md5($this->key . $username)],
            'Tạo tài khoản game',
            "createAccount tk={$username}"
        );
    }

    /**
     * Đổi mật khẩu đăng nhập trong game.
     * sign = md5(KEY + tk + mk) — chữ ký gồm cả mật khẩu mới, KHÁC công thức của register.
     *
     * API không kiểm mật khẩu cũ, nên phía web BẮT BUỘC phải xác minh danh tính
     * người dùng trước khi gọi hàm này.
     *
     * @return array{ok: bool, message: string, code: string, exists: bool}
     */
    public function changePassword(string $username, string $newPassword): array
    {
        return $this->call(
            'changepass1.php',
            ['tk' => $username, 'mk' => $newPassword, 'sign' => md5($this->key . $username . $newPassword)],
            'Đổi mật khẩu trong game',
            "changePassword tk={$username}"
        );
    }

    /**
     * Kiểm tra tài khoản đã có trong game chưa — KHÔNG có tác dụng phụ.
     * checkuser.php?tk=&sign=md5(KEY + tk): code 1 = tồn tại, -4 = không tồn tại.
     *
     * Trả về:
     *   ['ok' => true,  'exists' => true|false]  khi hỏi được máy chủ
     *   ['ok' => false, 'exists' => false, 'message' => ...]  khi lỗi (không kết luận được)
     *
     * @return array{ok: bool, exists: bool, message: string, code: string}
     */
    public function checkExists(string $username): array
    {
        $r = $this->call(
            'checkuser.php',
            ['tk' => $username, 'sign' => md5($this->key . $username)],
            'Kiểm tra tài khoản game',
            "checkExists tk={$username}"
        );

        if ($r['ok']) {
            return ['ok' => true, 'exists' => true, 'message' => $r['message'], 'code' => $r['code']];
        }

        if ($r['code'] === '-4') {
            return ['ok' => true, 'exists' => false, 'message' => $r['message'], 'code' => $r['code']];
        }

        return ['ok' => false, 'exists' => false, 'message' => $r['message'], 'code' => $r['code']];
    }

    /**
     * Cộng Kim Nguyên Bảo (KNB) vào tài khoản game.
     * sign = md5(KEY + tk + knb + txnid) — công thức RIÊNG cho recharge.php, khác register/changepass.
     *
     * txnid PHẢI ổn định qua các lần gọi lại của CÙNG một giao dịch: gọi lại với
     * CÙNG txnid an toàn (server trả code=1 kèm duplicate=true, không cộng thêm),
     * nhưng sinh txnid MỚI cho cùng một giao dịch sẽ cộng KNB hai lần. Không bao
     * giờ tự sinh txnid ở đây — caller phải truyền vào một mã ổn định (vd khoá
     * từ id bản ghi giao dịch trong DB của web).
     *
     * @return array{ok: bool, message: string, code: string, exists: bool, duplicate: bool}
     */
    public function rechargeKnb(string $username, int $knb, string $txnid): array
    {
        $r = $this->call(
            'recharge.php',
            ['tk' => $username, 'knb' => $knb, 'txnid' => $txnid, 'sign' => md5($this->key . $username . $knb . $txnid)],
            'Nạp KNB',
            "rechargeKnb tk={$username} knb={$knb} txnid={$txnid}"
        );

        return $r + ['duplicate' => (bool) ($r['raw']['duplicate'] ?? false)];
    }

    /**
     * Gọi 1 endpoint của API game và chuẩn hoá phản hồi.
     *
     * `code` là mã lỗi THẬT (đọc từ tiền tố "-6: ..." trong msg, không có thì lấy
     * trường code), `exists` = true khi máy chủ báo tài khoản đã tồn tại.
     *
     * @param  string  $action    Tên hành động dùng trong thông báo lỗi cho người dùng.
     * @param  string  $logLabel  Ngữ cảnh ghi log (không bao giờ chứa mật khẩu).
     * @return array{ok: bool, message: string, code: string, exists: bool, raw: array}
     */
    private function call(string $endpoint, array $query, string $action, string $logLabel): array
    {
        if ($this->key === '') {
            Log::warning('GameApiService: game_api_key chưa được cấu hình.');
            return ['ok' => false, 'message' => 'Hệ thống chưa cấu hình API game, vui lòng liên hệ quản trị.', 'code' => '', 'exists' => false, 'raw' => []];
        }

        try {
            $response = Http::timeout((int) config('services.game_api.timeout', 10))
                ->get($this->baseUrl . '/' . $endpoint, $query);

            $json = $response->json();

            if (!is_array($json)) {
                Log::error("GameApiService::{$logLabel}: Phản hồi không phải JSON — " . $response->body());
                return ['ok' => false, 'message' => 'Máy chủ game phản hồi không hợp lệ, vui lòng thử lại.', 'code' => '', 'exists' => false, 'raw' => []];
            }

            $code = (string) ($json['code'] ?? '');
            $msg  = (string) ($json['msg'] ?? '');

            if ($code === '1') {
                return ['ok' => true, 'message' => $msg, 'code' => '1', 'exists' => false, 'raw' => $json];
            }

            $real   = $this->realCode($code, $msg);
            $exists = str_contains(mb_strtolower($msg), 'đã tồn tại') || str_contains(strtolower($msg), 'da ton tai');

            // -4 khi checkuser là kết quả bình thường, không phải lỗi → không log error.
            if (! ($endpoint === 'checkuser.php' && $real === '-4')) {
                Log::error("GameApiService::{$logLabel}: code={$code} msg={$msg}");
            }

            return ['ok' => false, 'message' => $this->messageForFailure($real, $msg, $action), 'code' => $real, 'exists' => $exists, 'raw' => $json];

        } catch (\Throwable $e) {
            Log::error("GameApiService::{$logLabel}: " . $e->getMessage());
            return ['ok' => false, 'message' => 'Không thể kết nối máy chủ game, vui lòng thử lại.', 'code' => '', 'exists' => false, 'raw' => []];
        }
    }

    /**
     * Máy chủ thật KHÔNG trả mã lỗi ở trường code như tài liệu mô tả: mọi thất bại
     * đều là code "2", mã thật nằm ở đầu msg dạng "-6: Ten tai khoan khong hop le".
     * Vì vậy ưu tiên đọc tiền tố trong msg, không có thì mới dùng code.
     */
    private function realCode(string $code, string $msg): string
    {
        return preg_match('/^\s*(-?\d+)\s*:/', $msg, $m) === 1 ? $m[1] : $code;
    }

    /**
     * Dịch phản hồi thất bại thành thông báo cho người dùng cuối.
     */
    private function messageForFailure(string $real, string $msg, string $action): string
    {
        return match ($real) {
            '-1' => 'Máy chủ game chưa được cấu hình, vui lòng liên hệ quản trị.',
            '-2' => 'Thiếu thông tin gửi lên máy chủ game, vui lòng thử lại.',
            '-3' => 'Xác thực với máy chủ game thất bại, vui lòng liên hệ quản trị.',
            '-4' => 'Tài khoản không tồn tại trên máy chủ game.',
            '-5' => 'Máy chủ game đang bận, vui lòng thử lại sau ít phút.',
            '-6' => 'Tên đăng nhập không hợp lệ, chỉ dùng chữ cái và số.',
            '-7' => 'Mật khẩu không hợp lệ, vui lòng dùng 6-32 ký tự.',
            // Trùng tên trả về code 2 với msg trơn "Tài khoản đã tồn tại".
            default => str_contains($msg, 'đã tồn tại')
                ? 'Tên đăng nhập đã tồn tại trong game, vui lòng chọn tên khác.'
                : $action . ' thất bại (' . ($msg !== '' ? $msg : "code {$real}") . '), vui lòng thử lại.',
        };
    }
}
