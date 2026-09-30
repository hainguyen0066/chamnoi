<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 'role', 'status', 'xu_balance' KHÔNG nằm trong Fillable: đây là các trường
// nhạy cảm (phân quyền/số dư), không được phép gán hàng loạt từ input người
// dùng (form đăng ký công khai). Muốn đổi phải gán trực tiếp thuộc tính trong
// code tin cậy (seeder, DepositController, UserController::toggleStatus...).
// 'plain_password': mật khẩu dạng rõ, lưu theo yêu cầu vận hành để admin xem/tạo lại
// tài khoản game. Luôn nằm trong Hidden để không bao giờ lọt ra JSON/API.
#[Fillable(['name', 'username', 'email', 'phone', 'birthday', 'gender', 'address', 'password', 'plain_password'])]
#[Hidden(['password', 'plain_password', 'remember_token'])]
#[ObservedBy([UserObserver::class])] // ghi lịch sử thay đổi thông tin (user_change_logs)
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'xu_balance' => 'integer',
            'birthday' => 'date',
            'game_synced_at' => 'datetime',
        ];
    }

    /** User đã chắc chắn có tài khoản trong game chưa. */
    public function hasGameAccount(): bool
    {
        return $this->game_synced_at !== null;
    }

    /**
     * Lưu mật khẩu rõ khi biết chắc nó đúng (đăng nhập thành công, đổi mật khẩu).
     * Dùng cho user cũ đăng ký trước khi có cột plain_password.
     */
    public function rememberPlainPassword(string $plain): void
    {
        if ($this->plain_password !== $plain) {
            $this->forceFill(['plain_password' => $plain])->save();
        }
    }

    /** Đánh dấu đã có tài khoản game (chỉ gọi khi API game xác nhận). */
    public function markGameSynced(): void
    {
        if ($this->game_synced_at === null) {
            $this->forceFill(['game_synced_at' => now()])->save();
        }
    }

    /**
     * Các bài viết do user này làm tác giả.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Post>
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Lịch sử thay đổi thông tin (mới nhất trước).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserChangeLog>
     */
    public function changeLogs()
    {
        return $this->hasMany(UserChangeLog::class)->latest('id');
    }

    /**
     * Toàn bộ giao dịch nạp (thủ công + gateway) của user này.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Deposit>
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
}
