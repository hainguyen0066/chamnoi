<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\User;
use App\Services\VnpayService;
use App\Services\VnptPayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Quản lý nạp tiền:
 *  - index: danh sách toàn bộ giao dịch (nạp tay + gateway) = log audit.
 *  - create/store: form admin nạp tay cho user (Momo/Ngân hàng/CTV).
 *  - createPaymentLink: tạo link thanh toán thử VNPay/VNPT Pay (sandbox).
 *
 * TODO (banking tự động): khi chọn xong dịch vụ đối soát (Casso/SePay...),
 * thêm webhook controller ghi vào deposits với method=bank_transfer,
 * source=gateway — schema đã sẵn sàng, không cần đổi cấu trúc bảng.
 */
class DepositController extends Controller
{
    /**
     * Danh sách giao dịch nạp, lọc theo ngày / phương thức / trạng thái / từ khoá.
     */
    public function index(Request $request): View
    {
        $deposits = Deposit::query()
            ->with(['user', 'processedBy'])
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date('to'));
            })
            ->when($request->filled('method'), function ($query) use ($request) {
                $query->where('method', $request->string('method'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('account', 'like', '%' . $request->string('q') . '%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.deposits.index', compact('deposits'));
    }

    /**
     * Form nạp tiền cho user (nhập tay).
     */
    public function create(): View
    {
        return view('admin.deposits.form', [
            'vndPerXu' => Deposit::VND_PER_XU,
            'promotionPercents' => Deposit::PROMOTION_PERCENTS,
        ]);
    }

    /**
     * Lưu giao dịch nạp tay: tìm user theo tài khoản, TÍNH LẠI xu phía server,
     * tạo deposit completed và cộng số dư trong 1 DB transaction.
     */
    public function store(StoreDepositRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Tìm user theo thứ tự ưu tiên username → email → SĐT (khớp chính xác).
        // Không gộp bằng orWhere: 1 chuỗi có thể là username của user này nhưng
        // lại là SĐT của user khác — phải lấy đúng người theo thứ tự ưu tiên.
        $account = trim($data['account']);
        $user = null;
        foreach (['username', 'email', 'phone'] as $column) {
            $user = User::query()->where($column, $account)->first();
            if ($user !== null) {
                break;
            }
        }

        if ($user === null) {
            return back()
                ->withInput()
                ->with('error', "Không tìm thấy tài khoản \"{$account}\" (nhập tên tài khoản, email hoặc số điện thoại).");
        }

        // Điểm mấu chốt bảo mật: bỏ qua mọi giá trị "số xu nhận được" từ form,
        // luôn tính lại từ amount + promotion_percent phía server.
        $amountReceived = Deposit::calculateAmountReceived(
            (int) $data['amount'],
            (int) $data['promotion_percent']
        );

        $deposit = Deposit::query()->create([
            'user_id' => $user->id,
            'account' => $user->username ?: $account,
            'type' => $data['type'],
            'method' => $data['method'],
            'amount' => $data['amount'],
            'promotion_percent' => $data['promotion_percent'],
            'amount_received' => $amountReceived,
            'note' => $data['note'] ?? null,
            'status' => 'pending', // markCompleted() chuyển sang completed + cộng xu
            'source' => 'manual',
            'processed_by' => $request->user()->id,
        ]);

        $deposit->markCompleted();

        return redirect()
            ->route('admin.deposits.index')
            ->with('status', 'Đã nạp ' . number_format($amountReceived) . " KPoint cho {$user->username}.");
    }

    /**
     * Tạo link thanh toán thử qua cổng VNPay / VNPT Pay (test sandbox end-to-end).
     * Tạo deposit pending trước, callback IPN sẽ chuyển completed + cộng xu.
     */
    public function createPaymentLink(
        Request $request,
        VnpayService $vnpay,
        VnptPayService $vnptpay
    ): RedirectResponse {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'gateway' => ['required', 'in:vnpay,vnptpay'],
            'amount' => ['required', 'integer', 'min:10000', 'max:1000000000'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $amount = (int) $validated['amount'];
        $orderId = strtoupper($validated['gateway']) . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $deposit = Deposit::query()->create([
            'user_id' => $user->id,
            'account' => $user->email ?? $user->phone, // user đăng ký mới có thể chưa có email
            'type' => 'xu',
            'method' => $validated['gateway'],
            'amount' => $amount,
            'promotion_percent' => 0, // nạp qua gateway không áp khuyến mãi tay
            'amount_received' => Deposit::calculateAmountReceived($amount, 0),
            'status' => 'pending',
            'source' => 'gateway',
            'processed_by' => $request->user()->id,
            'gateway_transaction_id' => $orderId,
        ]);

        try {
            if ($validated['gateway'] === 'vnpay') {
                $paymentUrl = $vnpay->buildPaymentUrl(
                    $orderId,
                    $amount,
                    'Nap KPoint tai khoan ' . $user->id,
                    $request->ip()
                );

                return back()->with('payment_url', $paymentUrl);
            }

            $result = $vnptpay->createQr($orderId, $amount, 'Nap KPoint tai khoan ' . $user->id, $request->ip());

            if (! $result['success']) {
                $deposit->markFailed($result['data']);

                return back()->with('error', 'VNPT Pay từ chối: ' . $result['message']);
            }

            return back()
                ->with('status', 'Đã tạo giao dịch VNPT Pay.')
                ->with('payment_qr', $result['data']['QR_DATA'] ?? null)
                ->with('payment_url', $result['data']['PAYMENT_URL'] ?? null);
        } catch (\RuntimeException $e) {
            $deposit->markFailed(['error' => $e->getMessage()]);

            return back()->with('error', $e->getMessage());
        }
    }
}
