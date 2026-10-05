<?php

namespace App\Services;

use App\Models\GameLookupLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service gọi API Tra Cứu Người Chơi (Role <-> Account) GameServer v2.
 */
class GameLookupService
{
    private string $lookupUrl;
    private string $key;
    private int $timeout;

    public function __construct()
    {
        $this->lookupUrl = (string) (env('GAME_LOOKUP_API_URL') ?: config('services.game_kick.lookup_url', 'http://103.206.216.8:8090/v2/lookup.php'));
        $this->key = (string) (env('KICK_KEY') ?: env('GAME_KICK_KEY') ?: config('services.game_kick.key', ''));
        // Timeout 10s, chạy 1 lần duy nhất dứt điểm, không retry
        $this->timeout = (int) (env('GAME_LOOKUP_TIMEOUT') ?: 10);
    }

    public function getLookupUrl(): string
    {
        return $this->lookupUrl;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Tra cứu một đối tượng:
     * - by = 'role': Nhập tên nhân vật (UTF-8 có dấu), trả về tên tài khoản
     * - by = 'account': Nhập tên tài khoản, trả về danh sách nhân vật (tối đa 20)
     *
     * @param string $by 'role' hoặc 'account'
     * @param string $name Tên nhân vật hoặc tài khoản cần tra
     * @param string|null $adminName Tên admin thực hiện
     * @param int|null $adminId ID admin trong CSDL
     * @param bool $saveLog Có lưu lịch sử vào CSDL không
     * @return array{ok: bool, code: string, result: string, by: string, name: string, account: ?string, roles: array, msg: string, log?: GameLookupLog}
     */
    public function lookup(string $by, string $name, ?string $adminName = null, ?int $adminId = null, bool $saveLog = true): array
    {
        $by = in_array($by, ['role', 'account'], true) ? $by : 'role';
        $name = trim($name);

        if ($name === '') {
            return [
                'ok'      => false,
                'code'    => '2',
                'result'  => 'bad_request',
                'by'      => $by,
                'name'    => '',
                'account' => null,
                'roles'   => [],
                'msg'     => 'Vui lòng nhập tên cần tra cứu.',
            ];
        }

        if (empty($this->key)) {
            Log::error('GameLookupService: Chưa cấu hình KICK_KEY');
            return [
                'ok'      => false,
                'code'    => '2',
                'result'  => 'server_error',
                'by'      => $by,
                'name'    => $name,
                'account' => null,
                'roles'   => [],
                'msg'     => 'Hệ thống chưa cấu hình KICK_KEY bí mật.',
            ];
        }

        $ts = (string) time();

        // Chuỗi ký: lookup|<by>|<name>|<ts> (theo chuẩn HMAC-SHA256 UTF-8)
        $signPayload = "lookup|{$by}|{$name}|{$ts}";
        $sign = hash_hmac('sha256', $signPayload, $this->key);

        $postData = [
            'by'   => $by,
            'name' => $name,
            'ts'   => $ts,
            'sign' => $sign,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->lookupUrl, $postData);

            $data = $response->json();

            if (!is_array($data)) {
                $rawBody = $response->body();
                Log::warning('GameLookup API non-json response', ['status' => $response->status(), 'body' => $rawBody]);

                return [
                    'ok'      => false,
                    'code'    => '2',
                    'result'  => 'server_error',
                    'by'      => $by,
                    'name'    => $name,
                    'account' => null,
                    'roles'   => [],
                    'msg'     => 'GameServer phản hồi định dạng không hợp lệ (HTTP ' . $response->status() . ').',
                ];
            }

            $code = (string) ($data['code'] ?? '2');
            $result = (string) ($data['result'] ?? ($code === '1' ? 'ok' : 'unknown'));
            $msg = (string) ($data['msg'] ?? '');
            $account = isset($data['account']) ? (string) $data['account'] : null;
            $roles = isset($data['roles']) && is_array($data['roles']) ? array_values($data['roles']) : [];

            if ($code === '1') {
                if ($by === 'account' && empty($account)) {
                    $account = $name;
                }
            }

            $lookupLog = null;
            if ($saveLog) {
                try {
                    $lookupLog = GameLookupLog::create([
                        'by'           => $by,
                        'query_name'   => $name,
                        'admin_name'   => $adminName,
                        'admin_id'     => $adminId,
                        'code'         => $code,
                        'result'       => $result,
                        'account'      => $account,
                        'roles'        => $roles,
                        'msg'          => $msg,
                        'raw_response' => $data,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('GameLookupService: Không thể ghi log vào CSDL', ['error' => $e->getMessage()]);
                }
            }

            return [
                'ok'      => $code === '1',
                'code'    => $code,
                'result'  => $result,
                'by'      => $by,
                'name'    => $name,
                'account' => $account,
                'roles'   => $roles,
                'msg'     => $msg ?: ($code === '1' ? 'Tra cứu thành công.' : 'Không tìm thấy kết quả.'),
                'log'     => $lookupLog,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('GameLookup API connection error', ['error' => $e->getMessage()]);
            return [
                'ok'      => false,
                'code'    => '2',
                'result'  => 'server_error',
                'by'      => $by,
                'name'    => $name,
                'account' => null,
                'roles'   => [],
                'msg'     => 'Không thể kết nối tới GameServer (Timeout hoặc mạng lỗi).',
            ];
        } catch (\Throwable $e) {
            Log::error('GameLookup API exception', ['error' => $e->getMessage()]);
            return [
                'ok'      => false,
                'code'    => '2',
                'result'  => 'server_error',
                'by'      => $by,
                'name'    => $name,
                'account' => null,
                'roles'   => [],
                'msg'     => 'Lỗi hệ thống: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tra cứu hàng loạt danh sách. Mỗi mục chạy đúng 1 lần duy nhất, không retry nếu lỗi.
     *
     * @param string $by 'role' hoặc 'account'
     * @param array<string> $names Danh sách các tên cần tra
     * @param string|null $adminName
     * @param int|null $adminId
     * @return array<int, array>
     */
    public function lookupBatch(string $by, array $names, ?string $adminName = null, ?int $adminId = null): array
    {
        $results = [];
        $uniqueNames = array_values(array_unique(array_filter(array_map('trim', $names))));
        $total = count($uniqueNames);

        foreach ($uniqueNames as $index => $name) {
            if ($name === '') {
                continue;
            }
            // Gọi đúng 1 lần duy nhất
            $results[] = $this->lookup($by, $name, $adminName, $adminId, true);

            // Giãn cách nhẹ 0.3s giữa các request nếu còn mục tiếp theo để chống quá tải/rate-limit GameServer
            if ($index < $total - 1) {
                usleep(300000);
            }
        }

        return $results;
    }
}
