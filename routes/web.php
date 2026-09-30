<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BlockController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\DepositPackageController;
use App\Http\Controllers\Admin\KnbExchangeController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebhookLogController;
use Illuminate\Support\Facades\Route;

// Root: về trang login admin
Route::get('/', fn () => redirect()->route('admin.login'));

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (guest only)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // Khu vực quản trị — bắt buộc đăng nhập bằng guard admin
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::post('posts/upload-image', [PostController::class, 'uploadImage'])->name('posts.upload-image');
        Route::post('posts/ai-generate', [PostController::class, 'aiGenerate'])
            ->middleware('throttle:10,1')
            ->name('posts.ai-generate');
        Route::resource('posts', PostController::class)->except(['show']);

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        // 'users/create' phải đứng TRƯỚC 'users/{user}', nếu không sẽ bị route show nuốt mất.
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users/check-game', [UserController::class, 'checkGame'])->name('users.check-game');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/changes', [UserController::class, 'changes'])->name('users.changes');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
        Route::post('users/{user}/create-game-account', [UserController::class, 'createGameAccount'])->name('users.create-game-account');

        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::get('deposits/create', [DepositController::class, 'create'])->name('deposits.create');
        Route::post('deposits', [DepositController::class, 'store'])->name('deposits.store');
        Route::post('deposits/payment-link', [DepositController::class, 'createPaymentLink'])->name('deposits.payment-link');

        Route::get('deposit-packages', [DepositPackageController::class, 'index'])->name('deposit-packages.index');
        Route::put('deposit-packages', [DepositPackageController::class, 'update'])->name('deposit-packages.update');

        Route::get('knb-exchanges', [KnbExchangeController::class, 'index'])->name('knb-exchanges.index');
        Route::get('knb-exchanges/{knbExchange}', [KnbExchangeController::class, 'show'])->name('knb-exchanges.show');
        Route::post('knb-exchanges/{knbExchange}/retry', [KnbExchangeController::class, 'retry'])->middleware('throttle:20,1')->name('knb-exchanges.retry');

        Route::get('webhook-logs', [WebhookLogController::class, 'index'])->name('webhook-logs.index');
        Route::get('webhook-logs/{webhookLog}', [WebhookLogController::class, 'show'])->name('webhook-logs.show');

        Route::resource('sliders', SliderController::class)->except(['show']);

        Route::resource('blocks', BlockController::class)->except(['show']);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/maintenance', [SettingController::class, 'toggleMaintenance'])->name('settings.maintenance');
        Route::post('settings/deposit', [SettingController::class, 'toggleDeposit'])->name('settings.deposit');
        Route::post('settings/whitelist', [SettingController::class, 'saveWhitelist'])->name('settings.whitelist');
        Route::post('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.cache.clear');
        Route::post('settings/change-password', [SettingController::class, 'changePassword'])->name('settings.change-password');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media', [MediaController::class, 'destroy'])->name('media.destroy');
    });
});
