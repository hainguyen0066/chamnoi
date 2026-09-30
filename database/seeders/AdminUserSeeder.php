<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Tạo 1 tài khoản admin mặc định để đăng nhập lần đầu vào /admin.
 * Đổi mật khẩu ngay sau lần đăng nhập đầu tiên trên production.
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@volam.local']);

        $admin->name = 'Quản trị viên';
        $admin->password = Hash::make('password123');
        $admin->email_verified_at = now();
        $admin->save();

        // role/status không nằm trong $fillable nên gán trực tiếp thuộc tính.
        $admin->role = 'admin';
        $admin->status = 'active';
        $admin->save();
    }
}
