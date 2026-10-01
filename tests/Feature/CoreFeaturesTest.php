<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\GameKickLog;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CoreFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void

    {
        $response = $this->get('/admin/game-kicks');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_successfully(): void
    {
        $admin = Admin::create([
            'name'     => 'Test Admin',
            'email'    => 'test_admin_login@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'test_admin_login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin, 'admin');

        $admin->delete();
    }

    public function test_admin_cannot_login_with_wrong_password(): void
    {
        $admin = Admin::create([
            'name'     => 'Test Admin',
            'email'    => 'test_admin_wrong@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'test_admin_wrong@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');

        $admin->delete();
    }

    public function test_admin_can_access_account_management_and_create_users(): void
    {
        $admin = Admin::create([
            'name'     => 'Master Admin',
            'email'    => 'master_admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        // 1. Xem danh sách tài khoản
        $response = $this->actingAs($admin, 'admin')->get('/admin/accounts');
        $response->assertOk();
        $response->assertSee('Danh Sách Tài Khoản');

        // 2. Tạo tài khoản User mới
        $response = $this->actingAs($admin, 'admin')->post('/admin/accounts', [
            'name'                  => 'Gamer One',
            'email'                 => 'gamer_test_01@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
        ]);

        $response->assertRedirect('/admin/accounts');
        $this->assertDatabaseHas('users', [
            'email' => 'gamer_test_01@example.com',
            'role'  => 'user',
        ]);

        // 3. Tạo tài khoản Admin mới
        $response = $this->actingAs($admin, 'admin')->post('/admin/accounts', [
            'name'                  => 'Sub Admin',
            'email'                 => 'sub_admin_test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'admin',
        ]);

        $response->assertRedirect('/admin/accounts');
        $this->assertDatabaseHas('admins', [
            'email' => 'sub_admin_test@example.com',
        ]);

        // 4. Khóa tài khoản
        $createdUser = User::where('email', 'gamer_test_01@example.com')->first();
        $this->assertNotNull($createdUser);
        $toggleResp = $this->actingAs($admin, 'admin')->patch("/admin/accounts/{$createdUser->id}/toggle-status");
        $toggleResp->assertRedirect();
        $this->assertEquals('locked', $createdUser->fresh()->status);

        // 5. Xóa tài khoản
        $delResp = $this->actingAs($admin, 'admin')->delete("/admin/accounts/{$createdUser->id}");
        $delResp->assertRedirect();
        $this->assertDatabaseMissing('users', ['email' => 'gamer_test_01@example.com']);

        // Dọn dẹp
        User::where('email', 'sub_admin_test@example.com')->delete();
        Admin::where('email', 'sub_admin_test@example.com')->delete();
        $admin->delete();
    }

    public function test_two_factor_authentication_flow(): void
    {
        $admin = Admin::create([
            'name'     => '2FA Admin',
            'email'    => 'admin_2fa_test@example.com',
            'password' => Hash::make('secret123'),
        ]);

        // 1. Truy cập trang 2FA
        $response = $this->actingAs($admin, 'admin')->get('/admin/security/two-factor');
        $response->assertOk();
        $response->assertSee('Bảo Mật Tài Khoản');

        // 2. Kích hoạt 2FA với mã OTP đúng từ secret trong session
        $secret = session('admin_2fa_setup_secret');
        $this->assertNotEmpty($secret);
        $validCode = (new \PragmaRX\Google2FA\Google2FA())->getCurrentOtp($secret);

        $enableResp = $this->actingAs($admin, 'admin')->post('/admin/security/two-factor/enable', [
            'code' => $validCode,
        ]);
        $enableResp->assertRedirect();

        $admin->refresh();
        $this->assertTrue($admin->hasTwoFactorEnabled());
        $this->assertNotEmpty($admin->two_factor_recovery_codes);

        // 3. Đăng xuất và đăng nhập lại: Phải được chuyển hướng tới trang 2FA Challenge
        $this->post('/admin/logout');

        $loginResp = $this->post('/admin/login', [
            'email'    => 'admin_2fa_test@example.com',
            'password' => 'secret123',
        ]);
        $loginResp->assertRedirect(route('admin.2fa.challenge'));

        // 4. Nhập mã OTP vào trang 2FA Challenge để đăng nhập thành công
        $freshCode = (new \PragmaRX\Google2FA\Google2FA())->getCurrentOtp($secret);
        $challengeResp = $this->post(route('admin.2fa.challenge.submit'), [
            'code' => $freshCode,
        ]);
        $challengeResp->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');

        // 5. Tắt 2FA
        $disableResp = $this->actingAs($admin, 'admin')->post('/admin/security/two-factor/disable', [
            'password' => 'secret123',
        ]);
        $disableResp->assertRedirect();
        $admin->refresh();
        $this->assertFalse($admin->hasTwoFactorEnabled());

        $admin->delete();
    }


    public function test_game_kick_page_and_single_kick_validation(): void
    {
        $admin = Admin::create([
            'name'     => 'Kick Master',
            'email'    => 'kick_master@example.com',
            'password' => Hash::make('secret123'),
        ]);

        // 1. Mở trang Game Kick
        $response = $this->actingAs($admin, 'admin')->get('/admin/game-kicks');
        $response->assertOk();
        $response->assertSee('Kick Người Chơi');
        $response->assertSee('GameServer API v2');

        // 2. Validate dữ liệu không hợp lệ
        $invalidResp = $this->actingAs($admin, 'admin')->postJson('/admin/game-kicks', [
            'type'   => 'account',
            'name'   => '',
        ]);
        $invalidResp->assertStatus(422);

        $admin->delete();
    }
}
