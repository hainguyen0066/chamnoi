<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;
use App\Models\Slider;
use App\Services\CdnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CRUD slider đa vị trí (zone). Danh sách zone khai báo tại Slider::ZONES.
 */
class SliderController extends Controller
{
    /**
     * Danh sách slider, lọc theo vị trí.
     */
    public function index(Request $request): View
    {
        $sliders = Slider::query()
            ->when($request->filled('zone'), function ($query) use ($request) {
                $query->where('zone', $request->string('zone'));
            })
            ->orderBy('zone')
            ->orderBy('sort')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Form tạo slider.
     */
    public function create(): View
    {
        return view('admin.sliders.form', ['slider' => new Slider()]);
    }

    /**
     * Lưu slider mới, upload ảnh lên CDN.
     */
    public function store(StoreSliderRequest $request, CdnService $cdn): RedirectResponse
    {
        $data = $request->validated();

        try {
            $data['image'] = $cdn->upload($request->file('image'));

            if ($request->hasFile('mobile_image')) {
                $data['mobile_image'] = $cdn->upload($request->file('mobile_image'));
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', 'Upload ảnh thất bại: ' . $e->getMessage());
        }

        $data['status'] = $request->boolean('status');

        Slider::query()->create($data);

        return redirect()
            ->route('admin.sliders.index')
            ->with('status', 'Đã tạo slider.');
    }

    /**
     * Form sửa slider.
     */
    public function edit(Slider $slider): View
    {
        return view('admin.sliders.form', compact('slider'));
    }

    /**
     * Cập nhật slider, upload ảnh mới lên CDN nếu có.
     */
    public function update(UpdateSliderRequest $request, Slider $slider, CdnService $cdn): RedirectResponse
    {
        $data = $request->validated();

        try {
            foreach (['image', 'mobile_image'] as $field) {
                if ($request->hasFile($field)) {
                    // Xoá ảnh cũ nếu là local storage (CDN giữ nguyên)
                    if ($slider->{$field} && !str_starts_with($slider->{$field}, 'http')) {
                        Storage::disk('public')->delete($slider->{$field});
                    }
                    $data[$field] = $cdn->upload($request->file($field));
                } else {
                    unset($data[$field]);
                }
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', 'Upload ảnh thất bại: ' . $e->getMessage());
        }

        $data['status'] = $request->boolean('status');

        $slider->update($data);

        return redirect()
            ->route('admin.sliders.index')
            ->with('status', 'Đã cập nhật slider.');
    }

    /**
     * Xoá slider. Ảnh CDN (http) không xoá để tránh mất ảnh đang dùng.
     */
    public function destroy(Slider $slider): RedirectResponse
    {
        foreach (['image', 'mobile_image'] as $field) {
            if ($slider->{$field} && !str_starts_with($slider->{$field}, 'http')) {
                Storage::disk('public')->delete($slider->{$field});
            }
        }

        $slider->delete();

        return redirect()
            ->route('admin.sliders.index')
            ->with('status', 'Đã xoá slider.');
    }
}

