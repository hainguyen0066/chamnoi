<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(
        protected TwoFactorService $twoFactorService
    ) {}

    /**
     * Trang cài đặt bảo mật 2FA cho Admin hiện tại.
     */
    public function index(Request $request): View
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $appName = config('app.name', 'GAME API PANEL');

        $qrCodeSvg = null;
        $secret = null;

        if (! $admin->hasTwoFactorEnabled()) {
            // Lấy secret từ session hoặc tạo mới
            $secret = $request->session()->get('admin_2fa_setup_secret');
            if (! $secret) {
                $secret = $this->twoFactorService->generateSecretKey();
                $request->session()->put('admin_2fa_setup_secret', $secret);
            }

            $qrCodeSvg = $this->twoFactorService->getQrCodeSvg(
                appName: $appName,
                userEmail: $admin->email,
                secret: $secret
            );
        }

        return view('admin.security.two-factor', [
            'admin'         => $admin,
            'qrCodeSvg'     => $qrCodeSvg,
            'secret'        => $secret,
            'recoveryCodes' => $admin->two_factor_recovery_codes ?: [],
        ]);
    }

    /**
     * Kích hoạt 2FA sau khi quét mã và nhập mã xác thực.
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Vui lòng nhập mã 6 chữ số từ ứng dụng xác thực.',
            'code.size'     => 'Mã xác thực phải gồm đúng 6 chữ số.',
        ]);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $secret = $request->session()->get('admin_2fa_setup_secret');

        if (! $secret) {
            return back()->with('error', 'Phiên thiết lập đã hết hạn. Vui lòng tải lại trang để lấy mã QR mới.');
        }

        if (! $this->twoFactorService->verifyKey($secret, $request->input('code'))) {
            return back()->withErrors([
                'code' => 'Mã xác thực 6 số không chính xác hoặc đã hết hạn. Vui lòng kiểm tra lại giờ điện thoại và thử lại.',
            ]);
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        $admin->forceFill([
            'two_factor_secret'         => $secret,
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ])->save();

        $request->session()->forget('admin_2fa_setup_secret');

        return redirect()->route('admin.two-factor.index')->with('status', 'Chúc mừng! Bạn đã kích hoạt thành công Bảo mật 2 bước (2FA). Hãy lưu lại các mã khôi phục dự phòng bên dưới.');
    }

    /**
     * Tắt tính năng 2FA (yêu cầu nhập mật khẩu admin).
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu tài khoản để xác nhận tắt 2FA.',
        ]);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (! Hash::check($request->input('password'), $admin->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu tài khoản không chính xác.',
            ]);
        }

        $admin->forceFill([
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return redirect()->route('admin.two-factor.index')->with('status', 'Đã tắt tính năng bảo mật 2 bước (2FA) cho tài khoản của bạn.');
    }

    /**
     * Tạo lại danh sách mã khôi phục dự phòng mới.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (! $admin->hasTwoFactorEnabled()) {
            return back()->with('error', 'Tài khoản chưa bật 2FA.');
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
        $admin->forceFill(['two_factor_recovery_codes' => $recoveryCodes])->save();

        return back()->with('status', 'Đã tạo mới 8 mã khôi phục dự phòng thành công! Các mã cũ trước đây sẽ không còn hiệu lực.');
    }
}
