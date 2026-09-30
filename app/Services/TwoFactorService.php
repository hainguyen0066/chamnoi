<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Sinh khóa bí mật Base32 ngẫu nhiên (16 hoặc 32 ký tự).
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * Tạo mã QR dạng SVG để quét bằng ứng dụng Google Authenticator.
     */
    public function getQrCodeSvg(string $appName, string $userEmail, string $secret): string
    {
        $qrCodeUrl = $this->google2fa->getQRCodeUrl($appName, $userEmail, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200, 1),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        $svg = $writer->writeString($qrCodeUrl);

        // Bỏ thẻ xml header nếu có để chèn thẳng vào HTML
        if (str_starts_with($svg, '<' . '?xml')) {
            $pos = strpos($svg, '?>');
            if ($pos !== false) {
                $svg = substr($svg, $pos + 2);
            }
        }

        return trim($svg);
    }

    /**
     * Kiểm tra tính hợp lệ của mã 6 số (cho phép lệch ±1 bước 30s để chống lệch giờ mạng).
     */
    public function verifyKey(string $secret, string $code): bool
    {
        return (bool) $this->google2fa->verifyKey($secret, $code, 1);
    }

    /**
     * Sinh danh sách mã dự phòng (Recovery Codes).
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::random(10) . '-' . Str::random(10);
        }
        return $codes;
    }
}
