<?php

namespace App\Http\Controllers;

use App\Models\ScreeningQuestion;
use App\Models\ScreeningSubmission;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    public function index(Request $request)
    {
        $ageGroups = ScreeningQuestion::getAgeGroups();
        $selectedAge = $request->get('age', '18-24m');

        if (!array_key_exists($selectedAge, $ageGroups)) {
            $selectedAge = '18-24m';
        }

        $questions = ScreeningQuestion::where('is_active', true)
            ->where('age_group', $selectedAge)
            ->orderBy('order_index')
            ->get();

        return view('screening.index', compact('ageGroups', 'selectedAge', 'questions'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'age_group' => 'required|string',
            'child_age_months' => 'nullable|integer|min:6|max:72',
            'child_name' => 'nullable|string|max:100',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'answers' => 'required|array',
        ]);

        $questions = ScreeningQuestion::where('is_active', true)
            ->where('age_group', $validated['age_group'])
            ->get()
            ->keyBy('id');

        $score = 0;
        $redFlagsCount = 0;
        $totalQuestions = $questions->count();
        $answersDetails = [];

        foreach ($validated['answers'] as $qId => $answer) {
            if (isset($questions[$qId])) {
                $q = $questions[$qId];
                $isRisk = ($answer === 'no');

                // Trường hợp đặc biệt nếu câu hỏi đảo nghĩa (ví dụ: xem điện thoại nhiều = yes là risk)
                if (str_contains(mb_strtolower($q->question), 'thích xem điện thoại') || str_contains(mb_strtolower($q->question), 'hành vi lặp đi lặp lại')) {
                    $isRisk = ($answer === 'yes');
                }

                if ($isRisk) {
                    $score += 1;
                    if ($q->is_red_flag) {
                        $redFlagsCount += 1;
                    }
                }

                $answersDetails[$qId] = [
                    'question' => $q->question,
                    'answer' => $answer,
                    'is_risk' => $isRisk,
                    'is_red_flag' => $q->is_red_flag,
                ];
            }
        }

        // Đánh giá mức độ rủi ro (Risk Level)
        if ($redFlagsCount >= 2 || $score >= 3) {
            $riskLevel = 'high';
            $adviceSummary = 'Bé có dấu hiệu cờ đỏ hoặc nguy cơ chậm nói đáng kể. Khuyến nghị ba mẹ đưa bé đến bệnh viện chuyên khoa Nhi hoặc trung tâm can thiệp âm ngữ sớm để được đánh giá trực tiếp.';
        } elseif ($redFlagsCount == 1 || $score >= 1) {
            $riskLevel = 'medium';
            $adviceSummary = 'Bé có một vài chỉ số chậm hơn mốc phát triển thông thường. Ba mẹ cần tăng cường tương tác 1-1, áp dụng nguyên tắc 3T tại nhà và theo dõi sát sao trong 4-6 tuần.';
        } else {
            $riskLevel = 'low';
            $adviceSummary = 'Tuyệt vời! Bé đang có các phản xạ và tương tác phát triển phù hợp với lứa tuổi. Ba mẹ tiếp tục duy trì trò chuyện và đọc sách cùng con mỗi ngày nhé.';
        }

        $submission = ScreeningSubmission::create([
            'parent_name' => $validated['parent_name'] ?? 'Phụ huynh',
            'parent_phone' => $validated['parent_phone'] ?? null,
            'child_name' => $validated['child_name'] ?? 'Bé yêu',
            'child_age_months' => $validated['child_age_months'] ?? 18,
            'age_group' => $validated['age_group'],
            'answers' => $answersDetails,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'red_flags_count' => $redFlagsCount,
            'risk_level' => $riskLevel,
            'advice_summary' => $adviceSummary,
            'status' => 'new',
        ]);

        return redirect()->route('screening.result', $submission->id);
    }

    public function result(ScreeningSubmission $submission)
    {
        return view('screening.result', compact('submission'));
    }
}
