<?php

namespace App\Services;

/**
 * Tích hợp VNPay theo đặc tả chính thức (pay v2.1.0):
 *  - buildPaymentUrl(): dựng URL redirect sang trang thanh toán, ký HMAC-SHA512.
 *  - verifySignature(): xác thực chữ ký của return URL / IPN callback.
 *
 * Cấu hình .env: VNPAY_TMN_CODE, VNPAY_HASH_SECRET (đăng ký sandbox miễn phí
 * tại https://sandbox.vnpayment.vn), VNPAY_URL, VNPAY_RETURN_URL.
 */
class VnpayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $paymentUrl;
    private string $returnUrl;

    public function __construct()
    {
        $config = config('services.vnpay');

        $this->tmnCode = (string) $config['tmn_code'];
        $this->hashSecret = (string) $config['hash_secret'];
        $this->paymentUrl = (string) $config['url'];
        $this->returnUrl = (string) $config['return_url'];
    }

    /**
     * Đã đủ cấu hình để gọi VNPay chưa (dùng để hiện cảnh báo trong admin).
     */
    public function isConfigured(): bool
    {
        return $this->tmnCode !== '' && $this->hashSecret !== '';
    }

    /**
     * Dựng URL thanh toán VNPay cho một giao dịch nạp.
     *
     * @param string $txnRef mã giao dịch phía mình (deposits.gateway_transaction_id)
     * @param int $amount số tiền VND
     * @param string $orderInfo mô tả đơn hàng (không dấu, không ký tự đặc biệt)
     * @param string $clientIp IP người thanh toán
     * @return string URL để redirect người dùng sang VNPay
     * @throws \RuntimeException khi chưa cấu hình credentials
     */
    public function buildPaymentUrl(string $txnRef, int $amount, string $orderInfo, string $clientIp): string
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException(
                'Chưa cấu hình VNPAY_TMN_CODE / VNPAY_HASH_SECRET trong .env. '
                . 'Đăng ký sandbox miễn phí tại https://sandbox.vnpayment.vn để lấy.'
            );
        }

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $this->tmnCode,
            // VNPay quy định amount nhân 100 (đơn vị: xu VND)
            'vnp_Amount' => $amount * 100,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $txnRef,
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_IpAddr' => $clientIp,
            // VNPay yêu cầu giờ Việt Nam (GMT+7); app timezone là UTC nên phải chỉ định rõ
            'vnp_CreateDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            'vnp_ExpireDate' => now('Asia/Ho_Chi_Minh')->addMinutes(30)->format('YmdHis'),
        ];

        // Đặc tả VNPay: sort key tăng dần, urlencode từng phần tử (RFC1738,
        // space thành dấu +), nối bằng &, rồi HMAC-SHA512 trên chính chuỗi đó.
        // Dùng RFC3986 (space thành %20) sẽ bị VNPay báo sai chữ ký.
        ksort($params);
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC1738);
        $secureHash = hash_hmac('sha512', $query, $this->hashSecret);

        return $this->paymentUrl . '?' . $query . '&vnp_SecureHash=' . $secureHash;
    }

    /**
     * Xác thực chữ ký của dữ liệu VNPay gửi về (return URL hoặc IPN).
     *
     * @param array $params toàn bộ query params VNPay gửi về
     * @return bool true nếu chữ ký hợp lệ
     */
    public function verifySignature(array $params): bool
    {
        if (! $this->isConfigured() || empty($params['vnp_SecureHash'])) {
            return false;
        }

        $receivedHash = (string) $params['vnp_SecureHash'];

        // Loại các field không tham gia ký, chỉ giữ vnp_* rồi ký lại y hệt lúc gửi.
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        $params = array_filter(
            $params,
            fn ($key) => str_starts_with($key, 'vnp_'),
            ARRAY_FILTER_USE_KEY
        );

        ksort($params);
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC1738);
        $expectedHash = hash_hmac('sha512', $query, $this->hashSecret);

        // So sánh thời gian không đổi, chống timing attack.
        return hash_equals($expectedHash, $receivedHash);
    }

    /**
     * Giao dịch VNPay thành công khi ResponseCode và TransactionStatus đều '00'.
     */
    public function isSuccessResponse(array $params): bool
    {
        return ($params['vnp_ResponseCode'] ?? null) === '00'
            && ($params['vnp_TransactionStatus'] ?? null) === '00';
    }
}
