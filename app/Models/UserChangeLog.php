<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lịch sử thay đổi thông tin user — MỖI LẦN LƯU = 1 DÒNG.
 *  - action  : tóm tắt ("Thay đổi mật khẩu", "Cập nhật số điện thoại, địa chỉ")
 *  - changed_fields : {field: [old, new]} chi tiết, chỉ admin xem; mật khẩu không lưu giá trị.
 *    (không đặt tên cột là `changes` vì trùng thuộc tính nội bộ $changes của Eloquent Model)
 * Ghi tự động bởi App\Observers\UserObserver ở cả frontend lẫn admin.
 *
 * File này giống hệt nhau ở volam-laravel và volam-laravel-admin.
 */
#[Fillable(['user_id', 'action', 'changed_fields', 'actor_type', 'actor_id', 'actor_name', 'ip', 'user_agent'])]
class UserChangeLog extends Model
{
    public const UPDATED_AT = null;

    /** Tên hiển thị của các trường được theo dõi. Trường không có ở đây sẽ KHÔNG được ghi log. */
    public const FIELD_LABELS = [
        'name'           => 'họ tên',
        'username'       => 'tên đăng nhập',
        'email'          => 'email',
        'phone'          => 'số điện thoại',
        'birthday'       => 'ngày sinh',
        'gender'         => 'giới tính',
        'address'        => 'địa chỉ',
        'password'       => 'mật khẩu',
        'status'         => 'trạng thái',
        'role'           => 'vai trò',
        'game_synced_at' => 'tài khoản game',
    ];

    public const ACTOR_LABELS = [
        'user'   => 'Người dùng',
        'admin'  => 'Quản trị viên',
        'system' => 'Hệ thống',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'changed_fields' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actorLabel(): string
    {
        $type = self::ACTOR_LABELS[$this->actor_type] ?? $this->actor_type;

        return $this->actor_name ? "{$type}: {$this->actor_name}" : $type;
    }

    /**
     * Danh sách chi tiết [label, old, new] để admin xem. Mật khẩu chỉ ghi "đã đổi".
     *
     * @return list<array{label: string, old: string, new: string}>
     */
    public function details(): array
    {
        $out = [];
        foreach ((array) $this->changed_fields as $field => $pair) {
            [$old, $new] = is_array($pair) ? $pair + [null, null] : [null, $pair];
            $out[] = [
                'label' => self::mbUcfirst(self::FIELD_LABELS[$field] ?? $field),
                'old'   => self::formatValue($field, $old),
                'new'   => self::formatValue($field, $new),
            ];
        }

        return $out;
    }

    /** Trình duyệt / hệ điều hành rút gọn từ user agent, ví dụ "Chrome · Android". */
    public function browserLabel(): string
    {
        $ua = (string) $this->user_agent;
        if ($ua === '') {
            return '—';
        }

        $browser = match (true) {
            str_contains($ua, 'Edg/')                          => 'Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'SamsungBrowser')                => 'Samsung Internet',
            str_contains($ua, 'Chrome/') || str_contains($ua, 'CriOS') => 'Chrome',
            str_contains($ua, 'Firefox/') || str_contains($ua, 'FxiOS') => 'Firefox',
            str_contains($ua, 'Safari/')                       => 'Safari',
            str_contains($ua, 'Zalo')                          => 'Zalo',
            default                                            => 'Khác',
        };

        $os = match (true) {
            str_contains($ua, 'Android')                        => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Windows')                        => 'Windows',
            str_contains($ua, 'Mac OS')                         => 'macOS',
            str_contains($ua, 'Linux')                          => 'Linux',
            default                                             => '',
        };

        return $os !== '' ? "{$browser} · {$os}" : $browser;
    }

    /** Chuyển giá trị thô trong log thành chữ dễ đọc. */
    public static function formatValue(string $field, ?string $value): string
    {
        if ($field === 'password') {
            return '••••••••';
        }
        if ($value === null || $value === '') {
            return '—';
        }

        return match ($field) {
            'gender'         => ['1' => 'Nam', '2' => 'Nữ'][$value] ?? $value,
            'status'         => ['active' => 'Hoạt động', 'locked' => 'Đã khoá'][$value] ?? $value,
            'role'           => ['admin' => 'Quản trị', 'user' => 'Người chơi'][$value] ?? $value,
            'birthday'       => self::tryDate($value, 'd/m/Y'),
            'game_synced_at' => 'Đã xác nhận (' . self::tryDate($value, 'd/m/Y H:i') . ')',
            default          => $value,
        };
    }

    /** ucfirst hỗ trợ Unicode ("địa chỉ" → "Địa chỉ"). */
    public static function mbUcfirst(string $s): string
    {
        return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
    }

    private static function tryDate(string $value, string $format): string
    {
        try {
            return \Illuminate\Support\Carbon::parse($value)->format($format);
        } catch (\Throwable) {
            return $value;
        }
    }
}
