<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CdnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Quản lý ảnh trên CDN: upload, xem danh sách, xoá.
 */
class MediaController extends Controller
{
    public function __construct(private CdnService $cdn) {}

    public function index(): View
    {
        $files = $this->cdn->files();
        return view('admin.media.index', ['files' => $files]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        try {
            $url = $this->cdn->upload($request->file('file'));
            return response()->json(['success' => true, 'url' => $url]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        $path = $request->string('path')->toString();

        if (empty($path)) {
            return response()->json(['success' => false, 'error' => 'Path không được để trống'], 422);
        }

        $ok = $this->cdn->delete($path);
        return response()->json(['success' => $ok]);
    }
}
