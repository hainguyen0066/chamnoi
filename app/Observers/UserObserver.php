<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserChangeLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Ghi lịch sử thay đổi thông tin user vào bảng user_change_logs — MỖI LẦN LƯU = 1 DÒNG.
 *
 * Bắt ở tầng model nên mọi đường ghi (form frontend, admin, reset mật khẩu...)
 * đều được log mà không phải sửa từng controller. Chỉ theo dõi các trường trong
 * UserChangeLog::FIELD_LABELS; mật khẩu chỉ ghi "đã đổi" (không lưu giá trị);
 * plain_password không bao giờ được log.
 *
 * File này giống hệt nhau ở volam-laravel và volam-laravel-admin.
 */
class UserObserver
{
    public function created(User $user): void
    {
        // Lúc đăng ký user chưa đăng nhập → ghi người thực hiện là chính user
        // (trừ khi admin đang tạo hộ trong admin panel).
        $this->write($user, 'Đăng ký tài khoản', null, $user);
    }

    public function updated(User $user): void
    {
        $changes = [];
        foreach ($user->getChanges() as $field => $new) {
            if (! array_key_exists($field, UserChangeLog::FIELD_LABELS)) {
                continue;
            }

            if ($field === 'password') {
                $changes['password'] = [null, null]; // không lưu hash lẫn mật khẩu
                continue;
            }

            $old = $this->scalar($user->getOriginal($field));
            $new = $this->scalar($new);
            if ($old !== $new) {
                $changes[$field] = [$old, $new];
            }
        }

        if ($changes === []) {
            return;
        }

        $this->write($user, $this->describe($changes), $changes);
    }

    /**
     * Tóm tắt 1 dòng cho lần thay đổi, ví dụ:
     *  "Thay đổi mật khẩu" · "Khoá tài khoản" · "Cập nhật số điện thoại, địa chỉ"
     *  "Thay đổi mật khẩu; xác nhận tài khoản game"
     *
     * @param  array<string, array{0: ?string, 1: ?string}>  $changes
     */
    private function describe(array $changes): string
    {
        $parts  = [];
        $fields = [];

        foreach ($changes as $field => [$old, $new]) {
            switch ($field) {
                case 'password':
                    $parts[] = 'Thay đổi mật khẩu';
                    break;
                case 'status':
                    $parts[] = $new === 'locked' ? 'Khoá tài khoản' : 'Mở khoá tài khoản';
                    break;
                case 'game_synced_at':
                    $parts[] = 'Xác nhận tài khoản game';
                    break;
                default:
                    $fields[] = UserChangeLog::FIELD_LABELS[$field];
            }
        }

        if ($fields !== []) {
            array_unshift($parts, 'Cập nhật ' . implode(', ', $fields));
        }

        return ucfirst(implode('; ', array_map(fn ($p) => lcfirst($p), $parts)));
    }

    /**
     * @param  array<string, array{0: ?string, 1: ?string}>|null  $changes
     */
    private function write(User $user, string $action, ?array $changes, ?User $fallbackActor = null): void
    {
        try {
            [$type, $id, $name] = $this->actor();
            if ($type === 'system' && $fallbackActor !== null) {
                [$type, $id, $name] = ['user', $fallbackActor->id, $fallbackActor->username ?? $fallbackActor->name];
            }
            $req = app()->runningInConsole() ? null : request();

            UserChangeLog::query()->create([
                'user_id'    => $user->id,
                'action'     => $action,
                'changed_fields' => $changes,
                'actor_type' => $type,
                'actor_id'   => $id,
                'actor_name' => $name,
                'ip'         => $req?->ip(),
                'user_agent' => $req ? mb_substr((string) $req->userAgent(), 0, 512) : null,
            ]);
        } catch (\Throwable $e) {
            // Log lịch sử không được phép làm hỏng thao tác chính.
            Log::warning('UserObserver: không ghi được lịch sử thay đổi — ' . $e->getMessage());
        }
    }

    /**
     * Ai thực hiện thay đổi: admin (guard admin, chỉ có ở project admin),
     * chính user (guard web), còn lại là hệ thống (console, webhook, reset mật khẩu...).
     *
     * @return array{0: string, 1: int|null, 2: string|null}
     */
    private function actor(): array
    {
        if (config('auth.guards.admin') && ($admin = Auth::guard('admin')->user())) {
            return ['admin', $admin->id, $admin->name ?? $admin->email ?? null];
        }

        if (config('auth.guards.web') && ($me = Auth::guard('web')->user())) {
            return ['user', $me->id, $me->username ?? $me->name ?? null];
        }

        return ['system', null, null];
    }

    private function scalar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}
