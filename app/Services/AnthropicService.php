<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

/**
 * Gọi Anthropic (Claude) API — dùng cho tính năng "AI hỗ trợ viết bài"
 * trong form bài viết của admin.
 *
 * Cấu hình trong config/services.php (đọc từ .env):
 *  - anthropic.api_key : API key (bắt buộc)
 *  - anthropic.model   : model id, mặc định claude-sonnet-4-5
 *  - anthropic.verbose : true => log đầy đủ request/response để debug
 */
class AnthropicService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const API_VERSION = '2023-06-01';

    /**
     * Sinh nội dung bài viết từ chủ đề admin nhập.
     *
     * @param string $topic chủ đề / tiêu đề bài viết
     * @return string nội dung HTML do Claude sinh ra
     * @throws \RuntimeException khi thiếu API key hoặc API trả lỗi
     */
    public function generateContent(string $topic): string
    {
        $apiKey = Setting::get('anthropic_api_key') ?: config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException('Chưa cấu hình Anthropic API Key trong DB settings (tab AI Claude).');
        }

        $payload = [
            'model' => Setting::get('anthropic_model') ?: config('services.anthropic.model', 'claude-sonnet-4-5'),
            'max_tokens' => 2048,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Viết một bài tin tức bằng tiếng Việt cho website game Võ Lâm Truyền Kỳ về chủ đề: \"{$topic}\".\n\n"
                        . 'Yêu cầu: trả về HTML thuần (chỉ dùng thẻ <h2>, <h3>, <p>, <ul>, <li>, <strong>), '
                        . 'không kèm markdown, không lời dẫn ngoài bài, độ dài 300-500 từ, giọng văn hấp dẫn game thủ.',
                ],
            ],
        ];

        $this->logVerbose('Anthropic request', $payload);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => self::API_VERSION,
            ])
                ->timeout(60)
                ->post(self::API_URL, $payload);
        } catch (ConnectionException $e) {
            throw new \RuntimeException('Không kết nối được Anthropic API: ' . $e->getMessage());
        }

        $this->logVerbose('Anthropic response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if ($response->failed()) {
            $message = $response->json('error.message') ?? 'HTTP ' . $response->status();

            throw new \RuntimeException('Anthropic API lỗi: ' . $message);
        }

        $content = $response->json('content.0.text');

        if (! is_string($content) || $content === '') {
            throw new \RuntimeException('Anthropic API trả về nội dung rỗng.');
        }

        return $content;
    }

    /**
     * Log chi tiết khi bật anthropic.verbose. API key không bao giờ được log.
     */
    private function logVerbose(string $label, array $context): void
    {
        if (config('services.anthropic.verbose')) {
            Log::info($label, $context);
        }
    }
}
