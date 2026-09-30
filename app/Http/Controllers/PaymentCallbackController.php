<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Services\VnpayService;
use App\Services\VnptPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Nhận callback từ cổng thanh toán. Các route này public (cổng không đăng
 * nhập được) và được miễn CSRF — bảo vệ bằng xác thực chữ ký của từng cổng.
 *
 * Luồng chung: verify chữ ký -> tìm deposit theo gateway_transaction_id ->
 * markCompleted()/markFailed() (idempotent, cộng xu trong DB transaction).
 */
class PaymentCallbackController extends Controller
{
    /**
     * IPN VNPay (server-to-server). Phải trả JSON RspCode theo đặc tả để
     * VNPay biết mình đã nhận; trả sai mã họ sẽ retry liên tục.
     */
    public function vnpayIpn(Request $request, VnpayService $vnpay): JsonResponse
    {
        $params = $request->query();

        if (! $vnpay->verifySignature($params)) {
            Log::warning('VNPay IPN sai chữ ký', $params);

            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $deposit = Deposit::query()
            ->where('gateway_transaction_id', $params['vnp_TxnRef'] ?? '')
            ->first();

        if ($deposit === null) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        // Đối chiếu số tiền (VNPay gửi amount * 100) chống sửa giá trị.
        if ((int) ($params['vnp_Amount'] ?? 0) !== $deposit->amount * 100) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        if ($deposit->status !== 'pending') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($vnpay->isSuccessResponse($params)) {
            $deposit->markCompleted($params);
        } else {
            $deposit->markFailed($params);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm success']);
    }

    /**
     * Return URL VNPay (trình duyệt người dùng quay về sau khi thanh toán).
     * Chỉ hiển thị kết quả — việc cộng xu do IPN đảm nhiệm; nếu IPN đến trước
     * thì ở đây chỉ đọc trạng thái, không cộng lại (markCompleted idempotent).
     */
    public function vnpayReturn(Request $request, VnpayService $vnpay): View
    {
        $params = $request->query();
        $valid = $vnpay->verifySignature($params);

        $deposit = null;
        if ($valid) {
            $deposit = Deposit::query()
                ->where('gateway_transaction_id', $params['vnp_TxnRef'] ?? '')
                ->first();

            // Môi trường local/sandbox không nhận được IPN (VNPay không gọi vào
            // localhost) nên xử lý luôn tại return URL — chữ ký đã xác thực.
            if ($deposit !== null && $deposit->status === 'pending') {
                if ($vnpay->isSuccessResponse($params) && (int) ($params['vnp_Amount'] ?? 0) === $deposit->amount * 100) {
                    $deposit->markCompleted($params);
                } else {
                    $deposit->markFailed($params);
                }
            }
        }

        return view('payments.result', [
            'valid' => $valid,
            'success' => $valid && $vnpay->isSuccessResponse($params),
            'deposit' => $deposit?->fresh(),
        ]);
    }

    /**
     * IPN VNPT Pay (server-to-server), body JSON, response phải kèm chữ ký.
     */
    public function vnptpayIpn(Request $request, VnptPayService $vnptpay): JsonResponse
    {
        $post = $request->all();
        $orderId = (string) ($post['MERCHANT_ORDER_ID'] ?? '');

        if (! $vnptpay->verifyIpn($post)) {
            Log::warning('VNPT Pay IPN sai chữ ký', $post);

            return response()->json($vnptpay->buildIpnResponse('97', 'Invalid signature', $orderId));
        }

        $deposit = Deposit::query()
            ->where('gateway_transaction_id', $orderId)
            ->first();

        if ($deposit === null) {
            return response()->json($vnptpay->buildIpnResponse('01', 'Order not found', $orderId));
        }

        if ((int) ($post['AMOUNT'] ?? 0) !== $deposit->amount) {
            return response()->json($vnptpay->buildIpnResponse('04', 'Invalid amount', $orderId));
        }

        if ($deposit->status === 'pending') {
            if ($vnptpay->isSuccessResponse($post)) {
                $deposit->markCompleted($post);
            } else {
                $deposit->markFailed($post);
            }
        }

        return response()->json($vnptpay->buildIpnResponse('00', 'Confirm success', $orderId));
    }
}
