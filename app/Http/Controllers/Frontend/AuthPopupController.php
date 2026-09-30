<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\ZaloOAService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Popup đăng ký/đăng nhập frontend (AJAX JSON):
 *  - sendOtp/verifyOtp: xác thực số điện thoại qua Zalo OA ZNS.
 *  - register: đăng ký MỚI — bắt buộc phone đã verify OTP.
 *  - loginPopup: đăng nhập bằng username hoặc email.
 *  - zaloAuthorize/zaloOauthCallback: bootstrap token Zalo OA (1 lần, admin).
 */
class AuthPopupController extends Controller
{
    private const PHONE_REGEX = '/^(09|03|05|07|08)[0-9]{8}$/';

    /**
     * Gửi mã OTP qua Zalo ZNS cho số điện thoại đăng ký.
     */
    public function sendOtp(Request $request, ZaloOAService $zalo): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:' . self::PHONE_REGEX, 'unique:users,phone'],
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (10 số, đầu số 03/05/07/08/09).',
            'phone.unique' => 'Số điện thoại đã được sử dụng để đăng ký tài khoản khác.',
        ]);

        $phone = $validated['phone'];

        // Throttle: tối thiểu 60 giây giữa 2 lần gửi cho cùng 1 số.
        $last = PhoneVerification::findLatestByPhone($phone);
        if ($last !== null) {
            $secondsPassed = time() - $last->created_at->getTimestamp();
            if ($secondsPassed < PhoneVerification::OTP_RESEND_INTERVAL) {
                $wait = PhoneVerification::OTP_RESEND_INTERVAL - $secondsPassed;

                return response()->json([
                    'success' => false,
                    'message' => "Vui lòng đợi {$wait} giây trước khi gửi lại mã OTP.",
                ]);
            }
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $record = PhoneVerification::query()->create([
            'phone' => $phone,
            'otp' => $otp,
            'verified' => false,
            'expired_at' => time() + PhoneVerification::OTP_EXPIRE_SECONDS,
        ]);

        try {
            $sent = $zalo->sendOTP($phone, $otp);
        } catch (\Throwable $e) {
            Log::error('sendOtp: ' . $e->getMessage());
            $sent = false;
        }

        if (! $sent) {
            // Gửi thất bại thì xoá luôn mã vừa sinh, tránh để lại OTP "ảo" mà
            // người dùng không nhận được, chặn oan lần gửi kế bởi throttle.
            $record->delete();

            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi OTP qua Zalo, vui lòng thử lại sau.',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã gửi OTP qua Zalo.']);
    }

    /**
     * Xác thực mã OTP theo số điện thoại.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:' . self::PHONE_REGEX],
            'otp' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.regex' => 'Mã OTP gồm 6 chữ số.',
        ]);

        $record = PhoneVerification::findLatestByPhone($validated['phone']);

        if ($record === null) {
            return response()->json(['success' => false, 'message' => 'Vui lòng gửi mã OTP trước.']);
        }

        if ($record->expired_at < time()) {
            return response()->json(['success' => false, 'message' => 'OTP đã hết hạn, vui lòng gửi lại mã mới.']);
        }

        if ($record->attempts >= PhoneVerification::MAX_ATTEMPTS) {
            return response()->json(['success' => false, 'message' => 'Nhập sai quá nhiều lần, vui lòng gửi lại mã mới.']);
        }

        if (! hash_equals($record->otp, $validated['otp'])) {
            $record->increment('attempts');

            return response()->json(['success' => false, 'message' => 'OTP không đúng.']);
        }

        $record->update(['verified' => true]);

        return response()->json(['success' => true, 'message' => 'Xác thực số điện thoại thành công.']);
    }

    /**
     * Đăng ký tài khoản mới — chỉ khi phone đã verify OTP (trong 30 phút).
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:100'],
            'birthday' => ['required', 'date', 'before_or_equal:' . Carbon::now()->subYears(18)->toDateString()],
            'phone' => ['required', 'string', 'regex:' . self::PHONE_REGEX, 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'min:5', 'max:20', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'gender' => ['required', 'integer', 'in:1,2'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'fullname.required' => 'Vui lòng nhập họ tên.',
            'fullname.required' => 'Vui lòng nhập họ tên.',
            'birthday.required' => 'Vui lòng nhập ngày sinh.',
            'birthday.before_or_equal' => 'Người chơi đăng ký phải đủ 18 tuổi trở lên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (10 số, đầu số 03/05/07/08/09).',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.min' => 'Tên đăng nhập từ 5-20 ký tự.',
            'username.max' => 'Tên đăng nhập từ 5-20 ký tự.',
            'username.regex' => 'Tên đăng nhập chỉ gồm chữ, số và dấu gạch dưới.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Vui lòng chọn giới tính.',
        ]);

        // Điểm mấu chốt: số điện thoại BẮT BUỘC đã xác thực OTP.
        if (! PhoneVerification::isPhoneVerified($validated['phone'])) {
            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại chưa được xác thực OTP.',
            ]);
        }

        try {
            $user = DB::transaction(function () use ($validated) {
                $user = User::query()->create([
                    'name' => $validated['fullname'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'birthday' => $validated['birthday'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address'] ?? null,
                    'password' => Hash::make($validated['password']),
                ]);

                // OTP đã dùng xong, xoá để không tái sử dụng cho lần đăng ký khác.
                PhoneVerification::query()->where('phone', $validated['phone'])->delete();

                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('register popup: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Đăng ký thất bại, vui lòng thử lại.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công.',
            'redirect' => url('/'),
        ]);
    }

    /**
     * Đăng nhập từ popup bằng username hoặc email.
     */
    public function loginPopup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập hoặc email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $field = filter_var($validated['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $validated['username'], 'password' => $validated['password']])) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'redirect' => Auth::user()->role === 'admin' ? url('/admin') : url('/'),
        ]);
    }

    /**
     * Bootstrap 1 lần: redirect sang trang cấp quyền của Zalo.
     * Truy cập: /zalo-authorize?key=<setup_key trong .env>
     */
    public function zaloAuthorize(Request $request, ZaloOAService $zalo): RedirectResponse
    {
        $setupKey = (string) config('services.zalo_oa.setup_key');

        if ($setupKey === '' || $request->query('key') !== $setupKey) {
            abort(403, 'Không có quyền truy cập.');
        }

        return redirect()->away($zalo->getAuthorizeUrl($setupKey));
    }

    /**
     * Zalo redirect về đây sau khi admin cấp quyền, kèm "code".
     * "state" phải khớp setup_key.
     */
    public function zaloOauthCallback(Request $request, ZaloOAService $zalo)
    {
        $setupKey = (string) config('services.zalo_oa.setup_key');
        $code = (string) $request->query('code');

        if ($code === '' || $setupKey === '' || $request->query('state') !== $setupKey) {
            abort(403, 'Yêu cầu không hợp lệ.');
        }

        try {
            $zalo->exchangeCodeForToken($code);
            $message = 'Kết nối Zalo OA thành công. Bạn có thể đóng trang này.';
        } catch (\Throwable $e) {
            Log::error('zaloOauthCallback: ' . $e->getMessage());
            $message = 'Kết nối Zalo OA thất bại: ' . $e->getMessage();
        }

        return response('<h3>' . e($message) . '</h3>');
    }
}
