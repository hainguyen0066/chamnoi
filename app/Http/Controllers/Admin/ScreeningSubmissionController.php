<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningSubmission;
use Illuminate\Http\Request;

class ScreeningSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = ScreeningSubmission::query();

        if ($risk = $request->get('risk_level')) {
            $query->where('risk_level', $risk);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('child_name', 'like', "%{$search}%");
            });
        }

        $submissions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.screening-submissions.index', compact('submissions'));
    }

    public function show(ScreeningSubmission $screeningSubmission)
    {
        return view('admin.screening-submissions.show', compact('screeningSubmission'));
    }

    public function update(Request $request, ScreeningSubmission $screeningSubmission)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,reviewed,contacted',
            'admin_notes' => 'nullable|string',
        ]);

        $screeningSubmission->update($validated);

        return back()->with('success', 'Đã cập nhật trạng thái phiếu sàng lọc!');
    }

    public function destroy(ScreeningSubmission $screeningSubmission)
    {
        $screeningSubmission->delete();
        return redirect()->route('admin.screening-submissions.index')->with('success', 'Đã xóa phiếu sàng lọc!');
    }
}
