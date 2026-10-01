<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\GameKickController;
use App\Http\Controllers\Admin\TwoFactorController;
use Illuminate\Support\Facades\Route;

// Root: về game-kicks nếu đã đăng nhập, ngược lại về trang login admin
Route::get('/', fn () => auth('admin')->check()
    ? redirect()->route('admin.game-kicks.index')
    : redirect()->route('admin.login'));

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (guest only)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
        Route::get('/2fa-challenge',  [AdminAuthController::class, 'show2faChallenge'])->name('2fa.challenge');
        Route::post('/2fa-challenge', [AdminAuthController::class, 'verify2faChallenge'])->name('2fa.challenge.submit');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // Khu vực quản trị — bắt buộc đăng nhập bằng guard admin
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.game-kicks.index'))->name('dashboard');

        // 1. Kick Người Chơi (GameServer API v2)
        Route::get('game-kicks', [GameKickController::class, 'index'])->name('game-kicks.index');
        Route::post('game-kicks', [GameKickController::class, 'kick'])->middleware('throttle:30,1')->name('game-kicks.store');
        Route::post('game-kicks/{kickLog}/status', [GameKickController::class, 'checkStatus'])->name('game-kicks.status');

        // 2. Quản lý Tài khoản (Đăng ký / Danh sách / Khóa / Xóa)
        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::patch('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
        Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

        // 3. Cài đặt Bảo mật 2FA
        Route::get('security/two-factor', [TwoFactorController::class, 'index'])->name('two-factor.index');
        Route::post('security/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::post('security/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
        Route::post('security/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes');
    });
});

