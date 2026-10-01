<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function __construct(
        protected TwoFactorService $twoFactorService
    ) {}

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));
        $credentials['password'] = trim($credentials['password']);

        // Đồng bộ nếu tài khoản có trong bảng users với quyền admin nhưng chưa có ở admins
        if (! Admin::where('email', $credentials['email'])->exists()) {
            $user = \App\Models\User::where('email', $credentials['email'])->where('role', 'admin')->first();
            if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
                Admin::create([
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'password' => $user->password,
                ]);
            }
        }

        // Kiểm tra thông tin đăng nhập đúng hay sai
        if (! Auth::guard('admin')->validate($credentials)) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])->onlyInput('email');
        }

        /** @var Admin|null $admin */
        $admin = Admin::where('email', $credentials['email'])->first();

        if (! $admin) {
            return back()->withErrors([
                'email' => 'Tài khoản không tồn tại.',
            ])->onlyInput('email');
        }

        // Nếu admin ĐÃ kích hoạt 2FA -> Chuyển sang bước nhập mã xác thực 2FA
        if ($admin->hasTwoFactorEnabled()) {
            $request->session()->put('login.admin_2fa_id', $admin->id);
            $request->session()->put('login.admin_remember', $request->boolean('remember'));

            return redirect()->route('admin.2fa.challenge');
        }

        // Nếu admin CHƯA kích hoạt 2FA -> Đăng nhập thẳng bình thường
        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Màn hình thử thách 2FA (nhập mã 6 số từ Google Authenticator hoặc mã dự phòng).
     */
    public function show2faChallenge(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (! $request->session()->has('login.admin_2fa_id')) {
            return redirect()->route('admin.login');
        }

        $admin = Admin::find($request->session()->get('login.admin_2fa_id'));
        if (! $admin) {
            $request->session()->forget(['login.admin_2fa_id', 'login.admin_remember']);
            return redirect()->route('admin.login');
        }

        return view('admin.auth.two-factor-challenge', [
            'admin' => $admin,
        ]);
    }

    /**
     * Xác thực mã 2FA từ người dùng.
     */
    public function verify2faChallenge(Request $request): RedirectResponse
    {
        if (! $request->session()->has('login.admin_2fa_id')) {
            return redirect()->route('admin.login');
        }

        /** @var Admin $admin */
        $admin = Admin::find($request->session()->get('login.admin_2fa_id'));
        if (! $admin) {
            $request->session()->forget(['login.admin_2fa_id', 'login.admin_remember']);
            return redirect()->route('admin.login');
        }

        $request->validate([
            'code'          => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $code = trim((string) $request->input('code'));
        $recoveryCode = trim((string) $request->input('recovery_code'));

        $isVerified = false;

        // 1. Kiểm tra mã TOTP 6 số
        if (! empty($code)) {
            $isVerified = $this->twoFactorService->verifyKey($admin->two_factor_secret, $code);
        }

        // 2. Nếu không có code hoặc code sai, kiểm tra mã dự phòng recovery code
        if (! $isVerified && ! empty($recoveryCode)) {
            $recoveryCodes = $admin->two_factor_recovery_codes ?: [];
            $foundIndex = array_search($recoveryCode, $recoveryCodes, true);

            if ($foundIndex !== false) {
                $isVerified = true;
                // Xóa mã dự phòng này để không dùng lại được
                unset($recoveryCodes[$foundIndex]);
                $admin->forceFill([
                    'two_factor_recovery_codes' => array_values($recoveryCodes),
                ])->save();
            }
        }

        if (! $isVerified) {
            return back()->withErrors([
                'code' => 'Mã xác thực 2FA hoặc mã dự phòng không chính xác.',
            ]);
        }

        // Đăng nhập thành công
        $remember = (bool) $request->session()->get('login.admin_remember', false);
        $request->session()->forget(['login.admin_2fa_id', 'login.admin_remember']);

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
