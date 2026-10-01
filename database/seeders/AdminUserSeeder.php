<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Tạo tài khoản admin mặc định để đăng nhập vào /admin.
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultEmail = env('DEFAULT_ADMIN_EMAIL', 'admin@example.com');

        // Tạo tài khoản Admin cho bảng admins (dùng cho guard 'admin')
        $admin = Admin::firstOrNew(['email' => $defaultEmail]);
        $admin->name = 'Quản trị viên';
        $admin->password = Hash::make('password123');
        $admin->save();

        // Tạo tài khoản User cho bảng users (dùng cho guard 'web')
        $user = User::firstOrNew(['email' => $defaultEmail]);
        $user->name = 'Quản trị viên';
        $user->password = Hash::make('password123');
        $user->email_verified_at = now();
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();
    }
}
