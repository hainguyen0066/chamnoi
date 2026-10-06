<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterventionDay;
use Illuminate\Http\Request;

class InterventionDayController extends Controller
{
    public function index()
    {
        $days = InterventionDay::orderBy('day_number')->get();
        return view('admin.roadmap.index', compact('days'));
    }

    public function edit(InterventionDay $interventionDay)
    {
        return view('admin.roadmap.edit', compact('interventionDay'));
    }

    public function update(Request $request, InterventionDay $interventionDay)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'goal' => 'required|string',
            'activity_name' => 'required|string|max:255',
            'instructions' => 'required|string',
            'target_words' => 'nullable|string|max:255',
            'parent_tip' => 'required|string',
            'duration_minutes' => 'required|integer|min:5|max:120',
        ]);

        $interventionDay->update($validated);

        return redirect()->route('admin.roadmap.index')->with('success', 'Đã cập nhật bài tập Ngày ' . $interventionDay->day_number . ' thành công!');
    }
}
