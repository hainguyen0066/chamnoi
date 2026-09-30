<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use RuntimeException;

/**
 * Giao tiếp với CDN Image Server (cdnimg.tinhtrongthienha.vn).
 * Cấu hình qua CDN_URL và CDN_API_KEY trong .env.
 */
class CdnService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(Setting::get('cdn_url') ?: config('services.cdn.url', ''), '/');
        $this->apiKey  = Setting::get('cdn_api_key') ?: config('services.cdn.api_key', '');
    }

    /**
     * Upload file ảnh lên CDN, trả về URL public.
     *
     * @param string|null $name Tên file tùy chỉnh (vd: "maintenance.jpg"). Nếu null thì CDN tự tạo tên unique.
     */
    public function upload(UploadedFile $file, ?string $name = null): string
    {
        $this->assertConfigured();

        $multipart = ['file' => $file];
        if ($name !== null) {
            $multipart['name'] = $name;
        }

        try {
            $response = Http::withHeader('X-Api-Key', $this->apiKey)
                ->timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post($this->baseUrl . '/api/upload', $name ? ['name' => $name] : []);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Không kết nối được CDN server: ' . $e->getMessage());
        }

        $data = $response->json() ?? [];

        if (!$response->successful() || !($data['success'] ?? false)) {
            throw new RuntimeException('CDN upload thất bại: ' . ($data['error'] ?? $response->status()));
        }

        return $data['url'];
    }

    /**
     * Lấy danh sách tất cả ảnh đã upload, sắp xếp mới nhất lên đầu.
     *
     * @return array<int, array{url: string, path: string, filename: string, size: int, modified: int}>
     */
    public function files(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::withHeader('X-Api-Key', $this->apiKey)
                ->timeout(15)
                ->get($this->baseUrl . '/api/files');
        } catch (ConnectionException) {
            return [];
        }

        return $response->json('files', []);
    }

    /**
     * Xoá ảnh theo path (vd: "files/2026/08/abc.jpg").
     */
    public function delete(string $path): bool
    {
        $this->assertConfigured();

        try {
            $response = Http::withHeader('X-Api-Key', $this->apiKey)
                ->timeout(10)
                ->delete($this->baseUrl . '/api/delete', ['path' => $path]);
        } catch (ConnectionException) {
            return false;
        }

        return $response->successful() && ($response->json('success') ?? false);
    }

    private function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '';
    }

    private function assertConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('CDN chưa được cấu hình. Kiểm tra CDN_URL và CDN_API_KEY trong .env');
        }
    }
}
