<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InterventionDayController as AdminRoadmapController;
use App\Http\Controllers\Admin\MedicalCenterController as AdminMedicalCenterController;
use App\Http\Controllers\Admin\ScreeningQuestionController;
use App\Http\Controllers\Admin\ScreeningSubmissionController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MedicalCenterController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\ScreeningController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Mầm Ngôn Ngữ (Chậm Nói Ở Trẻ Em)
|--------------------------------------------------------------------------
*/

// --- FRONTEND ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kien-thuc', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/kien-thuc/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/phan-biet-cac-loai', [ArticleController::class, 'compare'])->name('articles.compare');

// Sàng lọc trực quan cho phụ huynh
Route::get('/sang-loc', [ScreeningController::class, 'index'])->name('screening.index');
Route::post('/sang-loc', [ScreeningController::class, 'submit'])->name('screening.submit');
Route::get('/sang-loc/ket-qua/{submission}', [ScreeningController::class, 'result'])->name('screening.result');

// Bộ phân tích hành vi & chấm điểm báo động đỏ y tế
Route::get('/phan-tich-hanh-vi', [\App\Http\Controllers\BehaviorAssessmentController::class, 'index'])->name('behavior-assessment.index');
Route::post('/phan-tich-hanh-vi', [\App\Http\Controllers\BehaviorAssessmentController::class, 'submit'])->name('behavior-assessment.submit');
Route::get('/phan-tich-hanh-vi/ket-qua/{submission}', [\App\Http\Controllers\BehaviorAssessmentController::class, 'result'])->name('behavior-assessment.result');

// Các công cụ can thiệp & đồng hành nâng cao
Route::get('/kiem-tra-von-tu', [\App\Http\Controllers\VocabularyCheckerController::class, 'index'])->name('vocabulary.index');
Route::get('/lo-trinh-30-ngay', [RoadmapController::class, 'index'])->name('roadmap.index');
Route::get('/tinh-tuoi-moc-chuan', [MilestoneController::class, 'index'])->name('milestones.index');
Route::get('/kich-am-flashcard', [FlashcardController::class, 'index'])->name('flashcards.index');
Route::get('/co-so-y-te', [MedicalCenterController::class, 'index'])->name('medical-centers.index');

// SEO Engine: Sitemap & Robots for Google Search Console
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

// Event & Click Tracking API
Route::post('/track-click', [\App\Http\Controllers\ClickTrackController::class, 'track'])->name('track.click');


// --- ADMIN ROUTES ---
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

    // Khu vực quản trị — Bắt buộc đăng nhập guard admin
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // 1. Quản lý Bài viết & Cẩm nang
        Route::resource('articles', AdminArticleController::class);

        // 2. Quản lý Bộ câu hỏi sàng lọc
        Route::resource('screening-questions', ScreeningQuestionController::class);

        // 3. Quản lý Phiếu kết quả sàng lọc gửi về từ phụ huynh
        Route::get('screening-submissions', [ScreeningSubmissionController::class, 'index'])->name('screening-submissions.index');
        Route::get('screening-submissions/{screeningSubmission}', [ScreeningSubmissionController::class, 'show'])->name('screening-submissions.show');
        Route::patch('screening-submissions/{screeningSubmission}', [ScreeningSubmissionController::class, 'update'])->name('screening-submissions.update');
        Route::delete('screening-submissions/{screeningSubmission}', [ScreeningSubmissionController::class, 'destroy'])->name('screening-submissions.destroy');

        // 4. Quản lý Lộ trình 30 ngày can thiệp
        Route::get('roadmap', [AdminRoadmapController::class, 'index'])->name('roadmap.index');
        Route::get('roadmap/{interventionDay}/edit', [AdminRoadmapController::class, 'edit'])->name('roadmap.edit');
        Route::put('roadmap/{interventionDay}', [AdminRoadmapController::class, 'update'])->name('roadmap.update');

        // 5. Quản lý Danh bạ Cơ sở y tế
        Route::resource('medical-centers', AdminMedicalCenterController::class);

        // 6. Thống kê Traffic & Lượt truy cập
        Route::get('traffic', [\App\Http\Controllers\Admin\TrafficAnalyticsController::class, 'index'])->name('traffic.index');

        // 8. Cấu hình Kiếm tiền & Treo quảng cáo (Google AdSense / Affiliate)
        Route::get('ads-settings', [\App\Http\Controllers\Admin\AdSettingController::class, 'index'])->name('ads.index');
        Route::post('ads-settings', [\App\Http\Controllers\Admin\AdSettingController::class, 'update'])->name('ads.update');

        // 9. Cấu hình Website
        Route::get('settings', [SiteSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SiteSettingController::class, 'update'])->name('settings.update');

        // 8. Quản lý Tài khoản Admin & Bảo mật 2FA
        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::patch('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
        Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

        Route::get('security/two-factor', [TwoFactorController::class, 'index'])->name('two-factor.index');
        Route::post('security/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::post('security/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
        Route::post('security/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes');
    });
});
