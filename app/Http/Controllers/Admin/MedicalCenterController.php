<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalCenter;
use Illuminate\Http\Request;

class MedicalCenterController extends Controller
{
    public function index()
    {
        $centers = MedicalCenter::orderBy('order_index')->get();
        return view('admin.medical-centers.index', compact('centers'));
    }

    public function create()
    {
        $cities = MedicalCenter::getCities();
        return view('admin.medical-centers.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'specialty' => 'required|string|max:255',
            'description' => 'nullable|string',
            'booking_tip' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'order_index' => 'nullable|integer',
        ]);

        $validated['is_verified'] = $request->boolean('is_verified', true);
        $validated['order_index'] = $validated['order_index'] ?? 0;

        MedicalCenter::create($validated);

        return redirect()->route('admin.medical-centers.index')->with('success', 'Đã thêm cơ sở y tế thành công!');
    }

    public function edit(MedicalCenter $medicalCenter)
    {
        $cities = MedicalCenter::getCities();
        return view('admin.medical-centers.edit', compact('medicalCenter', 'cities'));
    }

    public function update(Request $request, MedicalCenter $medicalCenter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'specialty' => 'required|string|max:255',
            'description' => 'nullable|string',
            'booking_tip' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
            'order_index' => 'nullable|integer',
        ]);

        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['order_index'] = $validated['order_index'] ?? 0;

        $medicalCenter->update($validated);

        return redirect()->route('admin.medical-centers.index')->with('success', 'Đã cập nhật cơ sở y tế!');
    }

    public function destroy(MedicalCenter $medicalCenter)
    {
        $medicalCenter->delete();
        return redirect()->route('admin.medical-centers.index')->with('success', 'Đã xóa cơ sở y tế!');
    }
}
