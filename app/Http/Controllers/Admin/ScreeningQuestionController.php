<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningQuestion;
use Illuminate\Http\Request;

class ScreeningQuestionController extends Controller
{
    public function index(Request $request)
    {
        $ageGroups = ScreeningQuestion::getAgeGroups();
        $selectedAge = $request->get('age_group', '12-18m');

        $questions = ScreeningQuestion::where('age_group', $selectedAge)
            ->orderBy('order_index')
            ->get();

        return view('admin.screening-questions.index', compact('questions', 'ageGroups', 'selectedAge'));
    }

    public function create(Request $request)
    {
        $ageGroups = ScreeningQuestion::getAgeGroups();
        $defaultAge = $request->get('age_group', '12-18m');
        return view('admin.screening-questions.create', compact('ageGroups', 'defaultAge'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'age_group' => 'required|string',
            'question' => 'required|string',
            'explanation' => 'nullable|string',
            'category' => 'required|string',
            'is_red_flag' => 'nullable|boolean',
            'points_yes' => 'nullable|integer',
            'points_no' => 'nullable|integer',
            'order_index' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_red_flag'] = $request->boolean('is_red_flag');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['points_yes'] = $validated['points_yes'] ?? 0;
        $validated['points_no'] = $validated['points_no'] ?? 1;
        $validated['order_index'] = $validated['order_index'] ?? 0;

        ScreeningQuestion::create($validated);

        return redirect()->route('admin.screening-questions.index', ['age_group' => $validated['age_group']])
            ->with('success', 'Đã thêm câu hỏi sàng lọc thành công!');
    }

    public function edit(ScreeningQuestion $screeningQuestion)
    {
        $ageGroups = ScreeningQuestion::getAgeGroups();
        return view('admin.screening-questions.edit', compact('screeningQuestion', 'ageGroups'));
    }

    public function update(Request $request, ScreeningQuestion $screeningQuestion)
    {
        $validated = $request->validate([
            'age_group' => 'required|string',
            'question' => 'required|string',
            'explanation' => 'nullable|string',
            'category' => 'required|string',
            'is_red_flag' => 'nullable|boolean',
            'points_yes' => 'nullable|integer',
            'points_no' => 'nullable|integer',
            'order_index' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_red_flag'] = $request->boolean('is_red_flag');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['points_yes'] = $validated['points_yes'] ?? 0;
        $validated['points_no'] = $validated['points_no'] ?? 1;
        $validated['order_index'] = $validated['order_index'] ?? 0;

        $screeningQuestion->update($validated);

        return redirect()->route('admin.screening-questions.index', ['age_group' => $validated['age_group']])
            ->with('success', 'Đã cập nhật câu hỏi sàng lọc!');
    }

    public function destroy(ScreeningQuestion $screeningQuestion)
    {
        $age = $screeningQuestion->age_group;
        $screeningQuestion->delete();

        return redirect()->route('admin.screening-questions.index', ['age_group' => $age])
            ->with('success', 'Đã xóa câu hỏi sàng lọc!');
    }
}
