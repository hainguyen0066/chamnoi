<?php

namespace App\Http\Controllers;

use App\Models\ScreeningSubmission;
use Illuminate\Http\Request;

class BehaviorAssessmentController extends Controller
{
    /**
     * Danh sách các Form trắc nghiệm hành vi chuyên sâu
     */
    public function index(Request $request)
    {
        $currentForm = $request->get('form', 'behavior_red_flags');
        $validForms = ['behavior_red_flags', 'mchat_r', 'receptive_hearing'];

        if (!in_array($currentForm, $validForms)) {
            $currentForm = 'behavior_red_flags';
        }

        $formData = $this->getFormData($currentForm);

        return view('behavior-assessment.index', compact('currentForm', 'formData'));
    }

    /**
     * Xử lý nộp bài và chấm điểm phân tích hành vi
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'form_type' => 'required|string|in:behavior_red_flags,mchat_r,receptive_hearing',
            'child_name' => 'nullable|string|max:100',
            'child_age_months' => 'required|integer|min:6|max:84',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'answers' => 'required|array',
        ]);

        $formType = $validated['form_type'];
        $childMonths = (int) $validated['child_age_months'];
        $formData = $this->getFormData($formType);
        $questions = $formData['questions'];

        $score = 0;
        $totalQuestions = count($questions);
        $answersDetails = [];
        $detectedRedFlags = [];

        foreach ($questions as $qId => $q) {
            $userAns = $validated['answers'][$qId] ?? null;
            $isRisk = false;

            // Xử lý logic tính điểm rủi ro theo từng loại form
            if ($formType === 'mchat_r') {
                // M-CHAT-R scoring logic
                // Câu hỏi đảo chiều (reverse): 10, 17, 19 -> 'yes' là nguy cơ
                $reverseQuestions = ['q10', 'q17', 'q19'];
                if (in_array($qId, $reverseQuestions)) {
                    $isRisk = ($userAns === 'yes');
                } else {
                    $isRisk = ($userAns === 'no');
                }
            } elseif ($formType === 'behavior_red_flags') {
                // Với bộ cờ đỏ, một số câu "Có" là rủi ro (stimming, rập khuôn, thoái lui, ăn vạ), một số câu "Không" là rủi ro (mắt, gọi tên, chỉ trỏ)
                if (isset($q['risk_answer'])) {
                    $isRisk = ($userAns === $q['risk_answer']);
                } else {
                    $isRisk = ($userAns === 'no');
                }
            } elseif ($formType === 'receptive_hearing') {
                if (isset($q['risk_answer'])) {
                    $isRisk = ($userAns === $q['risk_answer']);
                } else {
                    $isRisk = ($userAns === 'no');
                }
            }

            if ($isRisk) {
                $score += 1;
                if (!empty($q['is_red_flag'])) {
                    $detectedRedFlags[] = [
                        'code' => $qId,
                        'question' => $q['title'],
                        'category' => $q['category'] ?? 'Hành vi chung',
                        'flag_reason' => $q['flag_reason'] ?? 'Dấu hiệu cờ đỏ cần đặc biệt lưu ý.',
                        'severity' => $q['severity'] ?? 'high',
                    ];
                }
            }

            $answersDetails[$qId] = [
                'title' => $q['title'],
                'category' => $q['category'] ?? 'Chung',
                'user_answer' => $userAns,
                'is_risk' => $isRisk,
                'is_red_flag' => !empty($q['is_red_flag']) && $isRisk,
            ];
        }

        $redFlagsCount = count($detectedRedFlags);

        // --- PHÂN TÍCH KẾT QUẢ & ĐÁNH GIÁ NGUY CƠ ---
        $riskLevel = 'low';
        $needDoctor = false;
        $clinicalImpression = '';
        $doctorRecommendation = '';
        $adviceSummary = '';

        if ($formType === 'mchat_r') {
            // Theo hướng dẫn chính thức M-CHAT-R™ (Robins et al.):
            // 0 - 2: Thấp; 3 - 7: Trung bình; 8 - 20: Cao
            if ($score >= 8 || $redFlagsCount >= 3) {
                $riskLevel = 'high';
                $needDoctor = true;
                $clinicalImpression = 'Báo động nguy cơ cao Phổ Tự Kỷ (ASD) theo thang M-CHAT-R';
                $doctorRecommendation = '🚨 CẦN ĐƯA BÉ ĐI KHÁM BÁC SĨ CHUYÊN KHOA NGAY: Bé đạt từ 8 điểm rủi ro trở lên trên thang sàng lọc M-CHAT-R hoặc có nhiều cờ đỏ giao tiếp tương tác. Ba mẹ hãy đặt lịch hẹn khám tại Khoa Tâm thần Nhi / Khoa Phục hồi chức năng Bệnh viện Nhi Đồng hoặc Nhi Trung Ương trong vòng 1-2 tuần tới.';
                $adviceSummary = 'Bé có nhiều chỉ báo quan trọng về tương tác xã hội và giao tiếp tương hỗ bị chậm so với lứa tuổi. Can thiệp y khoa trong giai đoạn cửa sổ vàng trước 3 tuổi là chìa khóa then chốt giúp bé bắt kịp bạn bè.';
            } elseif ($score >= 3) {
                $riskLevel = 'medium';
                $needDoctor = ($redFlagsCount >= 1);
                $clinicalImpression = 'Nguy cơ trung bình - Cần thực hiện kiểm tra sâu hoặc khám chuyên gia';
                $doctorRecommendation = '⚠️ NÊN ĐƯỢC CHUYÊN GIA ĐÁNH GIÁ: Bé có điểm số ở mức trung bình (3 - 7 điểm). Khuyến nghị ba mẹ đưa bé đến chuyên viên âm ngữ trị liệu / bác sĩ nhi đánh giá thêm, hoặc áp dụng can thiệp tương tác tại nhà 4 tuần và thực hiện lại bài test.';
                $adviceSummary = 'Bé có một số điểm giao tiếp và chú ý chung chưa đạt chuẩn. Ba mẹ cần lập tức cắt toàn bộ điện thoại/tivi, tăng thời gian trò chuyện mặt đối mặt tối thiểu 2 giờ mỗi ngày.';
            } else {
                $riskLevel = 'low';
                $needDoctor = false;
                $clinicalImpression = 'Phát triển tương tác & hành vi phù hợp theo thang đo M-CHAT-R';
                $doctorRecommendation = '🟢 CHƯA CẦN KHÁM BÁC SĨ: Trẻ không có các dấu hiệu nguy cơ tự kỷ đáng kể trên thang đo này. Hãy tiếp tục theo dõi mốc ngôn ngữ tự nhiên theo lứa tuổi.';
                $adviceSummary = 'Bé đang có tương tác mắt, sự chú ý chung và phản xạ xã hội rất tốt. Ba mẹ tiếp tục duy trì đọc sách tranh và vui chơi ngoài trời cùng con nhé!';
            }
        } elseif ($formType === 'behavior_red_flags') {
            // Kiểm tra xem có cờ đỏ thoái lùi (regression) không
            $hasRegression = false;
            foreach ($detectedRedFlags as $rf) {
                if ($rf['code'] === 'q14_regression') {
                    $hasRegression = true;
                    break;
                }
            }

            if ($hasRegression || $redFlagsCount >= 2 || $score >= 6) {
                $riskLevel = 'high';
                $needDoctor = true;
                $clinicalImpression = $hasRegression
                    ? 'BÁO ĐỘNG ĐỎ TỐI KHẨN: Có dấu hiệu thoái lùi kỹ năng (Skill Regression)'
                    : 'Nghi ngờ Rối loạn Phổ Tự Kỷ (ASD) hoặc Rối loạn Giao tiếp Xã hội';
                $doctorRecommendation = '🚨 CẦN ĐI KHÁM BÁC SĨ CHUYÊN KHOA GẤP: ' . ($hasRegression ? 'Dấu hiệu thoái lui ngôn ngữ (từng biết nói/vẫy tay rồi bỗng nhiên mất hẳn) là cờ đỏ tối khẩn cấp trong y khoa nhi. Ba mẹ cần đưa bé đến bệnh viện chuyên khoa Nhi ngay lập tức.' : 'Bé xuất hiện từ 2 cờ đỏ hành vi hoặc điểm rủi ro cao. Ba mẹ hãy đưa bé đi khám Tâm thần nhi & đo thính lực ABR để loại trừ các nguyên nhân thực thể.');
                $adviceSummary = 'Bé có các hành vi đặc trưng như né tránh giao tiếp mắt, không phản ứng khi gọi tên, hành vi rập khuôn hoặc thoái lui kỹ năng. Can thiệp trước 3 tuổi có ý nghĩa sống còn.';
            } elseif ($redFlagsCount == 1 || $score >= 3) {
                $riskLevel = 'medium';
                $needDoctor = false;
                $clinicalImpression = 'Nghiêng về Chậm nói đơn thuần hoặc Rối loạn điều hòa cảm giác nhẹ';
                $doctorRecommendation = '⚠️ NÊN THEO DÕI SÁT & TƯ VẤN CHUYÊN GIA: Bé có 1 dấu hiệu cờ đỏ hoặc vài hành vi cần lưu ý. Ba mẹ có thể can thiệp tích cực tại nhà theo quy tắc 3T trong 4 tuần. Nếu sau 1 tháng không cải thiện, hãy cho bé đi khám bác sĩ.';
                $adviceSummary = 'Bé vẫn có giao tiếp mắt hoặc phản xạ nhưng vốn từ hoặc khả năng tương tác chưa tương xứng với tháng tuổi. Cần chú trọng kích âm và môi trường giao tiếp 1-1.';
            } else {
                $riskLevel = 'low';
                $needDoctor = false;
                $clinicalImpression = 'Hành vi và tương tác trong giới hạn bình thường';
                $doctorRecommendation = '🟢 CHƯA CẦN ĐI KHÁM BÁC SĨ: Bé không có dấu hiệu cờ đỏ nguy hiểm. Hãy tiếp tục môi trường gia đình giàu ngôn ngữ.';
                $adviceSummary = 'Các chỉ số hành vi, cảm xúc và giác quan của bé đang phát triển lành mạnh.';
            }
        } else {
            // Receptive & Hearing
            if ($score >= 5 || $redFlagsCount >= 2) {
                $riskLevel = 'high';
                $needDoctor = true;
                $clinicalImpression = 'Nghi ngờ Chậm ngôn ngữ tiếp nhận hoặc Khiếm thính / Giảm thính lực';
                $doctorRecommendation = '🚨 CẦN ĐI KHÁM BÁC SĨ TAI MŨI HỌNG & THÍNH HỌC NHI: Bé gặp khó khăn rõ rệt trong việc hiểu mệnh lệnh hoặc có dấu hiệu nghe kém. Bước đầu tiên quan trọng nhất là đo thính lực điện sinh lý (ABR/OAE) để kiểm tra khả năng nghe của bé.';
                $adviceSummary = 'Trẻ muốn nói được trước hết phải NGHE ĐƯỢC và HIỂU ĐƯỢC. Khám thính lực là chỉ định bắt buộc hàng đầu trước khi kết luận bé chậm nói do tâm lý hay bệnh lý.';
            } elseif ($score >= 2) {
                $riskLevel = 'medium';
                $needDoctor = false;
                $clinicalImpression = 'Nghiêng về Chậm ngôn ngữ biểu đạt đơn thuần (Hiểu tốt nhưng chưa nói)';
                $doctorRecommendation = '⚠️ THEO DÕI & TĂNG CƯỜNG HIỆU LỆNH TẠI NHÀ: Bé có thể hiểu được phần lớn mệnh lệnh nhưng vốn từ thụ động còn hạn chế. Tiếp tục nói chuyện chậm rãi, dùng câu ngắn và đọc sách tranh.';
                $adviceSummary = 'Bé có khả năng tiếp nhận nhưng phản xạ âm thanh hoặc chỉ dẫn cần được kiên nhẫn rèn luyện thêm mỗi ngày.';
            } else {
                $riskLevel = 'low';
                $needDoctor = false;
                $clinicalImpression = 'Khả năng hiểu lệnh & thính giác phát triển tốt';
                $doctorRecommendation = '🟢 CHƯA CẦN KHÁM BÁC SĨ: Khả năng nghe và nhận thức mệnh lệnh của con rất chuẩn xác theo lứa tuổi.';
                $adviceSummary = 'Bé hiểu lệnh tốt là nền tảng vững chắc để con sớm bùng nổ vốn từ biểu đạt trong thời gian tới.';
            }
        }

        // Tạo bản ghi submission
        $submission = ScreeningSubmission::create([
            'parent_name' => $validated['parent_name'] ?? 'Phụ huynh',
            'parent_phone' => $validated['parent_phone'] ?? null,
            'child_name' => $validated['child_name'] ?? 'Bé yêu',
            'child_age_months' => $childMonths,
            'age_group' => $this->convertMonthsToAgeGroup($childMonths),
            'form_type' => $formType,
            'answers' => $answersDetails,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'red_flags_count' => $redFlagsCount,
            'risk_level' => $riskLevel,
            'need_doctor' => $needDoctor,
            'clinical_impression' => $clinicalImpression,
            'detected_red_flags' => $detectedRedFlags,
            'doctor_recommendation' => $doctorRecommendation,
            'advice_summary' => $adviceSummary,
            'status' => 'new',
        ]);

        return redirect()->route('behavior-assessment.result', $submission->id);
    }

    /**
     * Hiển thị báo cáo kết quả đánh giá phân tích hành vi & cờ đỏ y tế
     */
    public function result(ScreeningSubmission $submission)
    {
        $formInfo = $this->getFormMeta($submission->form_type);

        return view('behavior-assessment.result', compact('submission', 'formInfo'));
    }

    /**
     * Dữ liệu các bộ form
     */
    private function getFormData(string $formType): array
    {
        return match ($formType) {
            'mchat_r' => $this->getMchatQuestions(),
            'receptive_hearing' => $this->getReceptiveQuestions(),
            default => $this->getBehaviorRedFlagsQuestions(),
        };
    }

    private function getFormMeta(string $formType): array
    {
        return match ($formType) {
            'mchat_r' => [
                'title' => 'Thang Sàng Lọc M-CHAT-R™ Quốc Tế',
                'age_text' => 'Trẻ từ 16 - 30 tháng tuổi',
                'description' => 'Bộ câu hỏi chuẩn y khoa quốc tế sàng lọc sớm phổ tự kỷ & rối loạn giao tiếp xã hội.',
            ],
            'receptive_hearing' => [
                'title' => 'Bảng Đánh Giá Hiểu Lệnh & Thính Lực',
                'age_text' => 'Trẻ từ 12 - 36 tháng tuổi',
                'description' => 'Phân biệt chậm nói đơn thuần với vấn đề suy giảm thính lực hoặc chậm tiếp nhận.',
            ],
            default => [
                'title' => 'Bảng Phân Tích Hành Vi & Cờ Đỏ Toàn Diện',
                'age_text' => 'Trẻ từ 12 tháng - 5 tuổi',
                'description' => 'Khảo sát 4 trục hành vi quan sát thực tế tại nhà để phát hiện dấu hiệu báo động đỏ.',
            ],
        };
    }

    private function convertMonthsToAgeGroup(int $months): string
    {
        if ($months < 18) return '12-18m';
        if ($months < 24) return '18-24m';
        if ($months < 36) return '2-3y';
        return '3-5y';
    }

    /**
     * Bộ câu hỏi 1: Phân Tích Hành Vi & Dấu Hiệu Cờ Đỏ Toàn Diện
     */
    private function getBehaviorRedFlagsQuestions(): array
    {
        return [
            'meta' => [
                'title' => 'Bảng Phân Tích Hành Vi & Cờ Đỏ Toàn Diện',
                'target_age' => 'Dành cho trẻ từ 12 tháng đến 5 tuổi',
                'badge' => 'Phân tích 4 trục hành vi',
                'desc' => 'Tập trung quan sát các hành vi thực tế hằng ngày: Giao tiếp mắt, quay đầu khi gọi tên, hành vi rập khuôn, giác quan và dấu hiệu thoái lui.',
            ],
            'questions' => [
                // Trục 1: Tương tác Xã hội & Cảm xúc
                'q1_eye_contact' => [
                    'category' => 'Tương tác Xã hội & Ánh mắt',
                    'title' => 'Khi bạn nói chuyện hoặc chơi với bé ở cự ly gần, bé có nhìn thẳng vào mắt bạn (duy trì trên 2-3 giây) không?',
                    'desc' => 'Trẻ bình thường sẽ tìm kiếm ánh mắt cha mẹ để chia sẻ niềm vui hoặc đòi hỏi. Trẻ có nguy cơ thường né tránh ánh mắt, nhìn lướt qua hoặc nhìn như xuyên qua người đối diện.',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Không duy trì giao tiếp mắt hoặc né tránh ánh mắt khi tương tác.',
                    'severity' => 'high',
                ],
                'q2_social_smile' => [
                    'category' => 'Tương tác Xã hội & Ánh mắt',
                    'title' => 'Bé có mỉm cười đáp lại khi bạn cười hoặc trêu đùa với bé (nụ cười xã hội) không?',
                    'desc' => 'Nụ cười xã hội xuất hiện từ 2-3 tháng tuổi. Trẻ có nguy cơ thường ít cười đáp lại người khác, hoặc cười một mình vô cớ không liên quan hoàn cảnh.',
                    'risk_answer' => 'no',
                    'is_red_flag' => false,
                ],
                'q3_name_response' => [
                    'category' => 'Tương tác Xã hội & Ánh mắt',
                    'title' => 'Khi bạn gọi tên bé (trong lúc bé KHÔNG xem tivi hay điện thoại), bé có phản xạ quay đầu lại nhìn bạn không?',
                    'desc' => 'Trẻ phát triển bình thường sẽ quay đầu đáp lại 8/10 lần gọi. Nếu gọi 5-10 lần với âm lượng rõ ràng mà bé như "điếc", phớt lờ hoàn toàn thì đây là cờ đỏ rất lớn.',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Không quay đầu lại khi người thân gọi tên (phớt lờ tên gọi).',
                    'severity' => 'high',
                ],
                'q4_social_sharing' => [
                    'category' => 'Tương tác Xã hội & Ánh mắt',
                    'title' => 'Bé có bao giờ mang một món đồ chơi hoặc đồ vật tìm được lại gần để khoe với bạn hoặc rủ bạn cùng chơi không?',
                    'desc' => 'Hành vi mang đồ khoe biểu thị nhu cầu chia sẻ cảm xúc (Joint Attention). Nếu bé chỉ chơi một mình, không bao giờ đem khoe ai thì cần lưu ý.',
                    'risk_answer' => 'no',
                    'is_red_flag' => false,
                ],

                // Trục 2: Hành vi Rập khuôn & Giác quan
                'q5_stimming' => [
                    'category' => 'Hành vi Rập khuôn & Giác quan',
                    'title' => 'Bé có các hành vi lặp đi lặp lại như: vẫy hai bàn tay như cánh bướm khi phấn khích, xoay tròn người hoặc thường xuyên đi nhón gót chân không?',
                    'desc' => 'Đây là các hành vi tự kích thích (stimming) thường gặp ở trẻ rối loạn giác quan hoặc phổ tự kỷ.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Xuất hiện hành vi rập khuôn: vẫy tay cánh bướm, xoay tròn người hoặc đi nhón gót.',
                    'severity' => 'high',
                ],
                'q6_spinning_wheels' => [
                    'category' => 'Hành vi Rập khuôn & Giác quan',
                    'title' => 'Khi chơi ô tô hoặc đồ chơi có bánh xe, bé có hay lật ngửa xe để xoay tròn bánh xe hàng giờ, hoặc mê mẩn nhìn quạt quay, máy giặt quay không?',
                    'desc' => 'Bé không chơi xe đúng chức năng (đẩy xe chạy giả vờ) mà bị thu hút quá mức bởi chi tiết quay tròn của bánh xe.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Chơi đồ chơi rập khuôn, ám ảnh xoay bánh xe hoặc chuyển động quay.',
                    'severity' => 'high',
                ],
                'q7_sensory_sound' => [
                    'category' => 'Hành vi Rập khuôn & Giác quan',
                    'title' => 'Bé có phản ứng sợ hãi tột cùng, bịt hai tai hoặc khóc thét trước các âm thanh sinh hoạt bình thường (tiếng máy sấy tóc, máy hút bụi, máy xay sinh tố)?',
                    'desc' => 'Dấu hiệu của chứng tăng nhạy cảm thính giác (Auditory Sensory Over-responsivity).',
                    'risk_answer' => 'yes',
                    'is_red_flag' => false,
                ],
                'q8_lining_objects' => [
                    'category' => 'Hành vi Rập khuôn & Giác quan',
                    'title' => 'Bé có thói quen xếp đồ chơi hoặc đồ vật thành một hàng dài ngay ngắn, và nổi giận dữ dội nếu ai vô tình chạm vào làm lệch hàng không?',
                    'desc' => 'Tính rập khuôn, cứng nhắc và nhu cầu kiểm soát sự đồng nhất cao.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Hành vi xếp đồ chơi thành hàng dài rập khuôn, chống đối việc thay đổi.',
                    'severity' => 'high',
                ],
                'q9_temper_tantrums' => [
                    'category' => 'Hành vi Rập khuôn & Giác quan',
                    'title' => 'Bé có thường xuyên ăn vạ mất kiểm soát kéo dài, tự cắn tay mình hoặc đập đầu vào sàn nhà/tường khi không vừa ý?',
                    'desc' => 'Hành vi tự gây thương tích và khó khăn cực độ trong việc điều hòa cảm xúc.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Ăn vạ bùng nổ dữ dội, có hành vi tự cắn hoặc đập đầu vào tường/sàn.',
                    'severity' => 'high',
                ],

                // Trục 3: Cử chỉ Giao tiếp & Bắt chước
                'q10_pointing' => [
                    'category' => 'Cử chỉ Giao tiếp Chức năng',
                    'title' => 'Bé có biết dùng NGÓN TRỎ để chỉ vào đồ vật để xin xỏ ("mẹ ơi lấy cho con") hoặc chỉ để khoe ("con mèo kìa") không?',
                    'desc' => 'Ngón trỏ là cột mốc phát triển ngôn ngữ tối quan trọng từ 12-14 tháng. Trẻ 18 tháng chưa biết chỉ ngón trỏ là cờ đỏ báo động.',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Không biết dùng ngón trỏ để chỉ vào đồ vật nhằm yêu cầu hoặc chia sẻ chú ý.',
                    'severity' => 'high',
                ],
                'q11_hand_tool' => [
                    'category' => 'Cử chỉ Giao tiếp Chức năng',
                    'title' => 'Khi muốn lấy đồ trên cao hoặc mở nắp hộp, bé có nắm cổ tay bạn kéo đi như một "công cụ vô tri" và đặt tay bạn vào đồ vật đó mà không thèm nhìn mắt bạn không?',
                    'desc' => 'Dắt tay người lớn như một công cụ cơ học (leading by the hand) mà không có tương tác mắt hoặc cử chỉ kết nối.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Sử dụng bàn tay người lớn như một công cụ vô tri mà không có giao tiếp mắt.',
                    'severity' => 'high',
                ],
                'q12_gestures_common' => [
                    'category' => 'Cử chỉ Giao tiếp Chức năng',
                    'title' => 'Bé có biết làm các cử chỉ cơ bản như: vẫy tay "bye bye", vỗ tay hoan hô, gật đầu đồng ý, lắc đầu từ chối không?',
                    'desc' => 'Giao tiếp phi ngôn ngữ đi trước lời nói. Trẻ chậm cử chỉ thường có nguy cơ cao chậm ngôn ngữ nghiêm trọng.',
                    'risk_answer' => 'no',
                    'is_red_flag' => false,
                ],
                'q13_follow_pointing' => [
                    'category' => 'Cử chỉ Giao tiếp Chức năng',
                    'title' => 'Khi bạn chỉ tay vào một con vật hoặc đồ chơi ở xa và nói "Nhìn kìa con!", bé có nhìn theo hướng ngón tay của bạn không?',
                    'desc' => 'Khả năng theo dõi hướng ngón tay (Joint Attention). Nếu bé chỉ nhìn chằm chằm vào đầu ngón tay bạn hoặc không để ý thì chưa đạt.',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Không có phản xạ nhìn theo hướng ngón tay chỉ của người lớn.',
                    'severity' => 'high',
                ],

                // Trục 4: Dấu hiệu Thoái lui Nguy Cấp
                'q14_regression' => [
                    'category' => 'CỜ ĐỎ TỐI KHẨN CẤP: THOÁI LUI KỸ NĂNG',
                    'title' => 'Bé có từng biết nói một vài từ đơn (ba, mẹ, măm) hoặc từng biết vẫy tay chào, nhưng sau đó bỗng nhiên MẤT HẲN khả năng này không?',
                    'desc' => 'Thoái lùi kỹ năng (Skill Regression) là dấu hiệu báo động nghiêm trọng nhất trong y khoa phát triển nhi, cần đi khám bác sĩ ngay.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => '🚨 THOÁI LUI KỸ NĂNG: Từng nói được từ hoặc có cử chỉ nhưng bỗng nhiên bị mất hẳn.',
                    'severity' => 'critical',
                ],
                'q15_selective_hearing' => [
                    'category' => 'Tương tác Xã hội & Cảm xúc',
                    'title' => 'Bé có biểu hiện như bị "điếc chọn lọc" (gọi tên sát bên tai thì không nghe, nhưng tiếng mở bọc bánh kẹo hoặc nhạc quảng cáo ở phòng khác thì lập tức chạy lại)?',
                    'desc' => 'Phản ứng này chứng tỏ thính lực bình thường nhưng có sự khiếm khuyết trong việc tiếp nhận và tương tác với tiếng người.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Biểu hiện điếc chọn lọc (lờ đi tiếng gọi người, nhưng nhạy với âm thanh tivi/đồ ăn).',
                    'severity' => 'high',
                ],
                'q16_social_isolation' => [
                    'category' => 'Tương tác Xã hội & Cảm xúc',
                    'title' => 'Bé có xu hướng tự cô lập bản thân, chỉ thích chơi một mình trong góc, người lạ đến hay người thân đi đều không có phản ứng cảm xúc?',
                    'desc' => 'Thiếu sự gắn bó an toàn và nhu cầu tương tác xã hội tự nhiên với người chăm sóc.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => false,
                ],
            ]
        ];
    }

    /**
     * Bộ câu hỏi 2: Thang Sàng Lọc M-CHAT-R™ Quốc Tế (16 - 30 tháng)
     */
    private function getMchatQuestions(): array
    {
        return [
            'meta' => [
                'title' => 'Thang Đo Sàng Lọc Tự Kỷ Sớm M-CHAT-R™',
                'target_age' => 'Dành riêng cho trẻ từ 16 đến 30 tháng tuổi',
                'badge' => 'Chuẩn y khoa quốc tế',
                'desc' => 'Bản quyền nghiên cứu bởi GS. Diana Robins, Deborah Fein & Marianne Barton. Được Viện Hàn Lâm Nhi Khoa Hoa Kỳ (AAP) khuyến nghị sử dụng trong các đợt khám định kỳ 18 và 24 tháng.',
            ],
            'questions' => [
                'q1' => ['title' => 'Bé có thích được đung đưa, nhún nhảy trên đầu gối của bạn không?', 'is_red_flag' => false],
                'q2' => ['title' => 'Bé có quan tâm hoặc để ý đến những đứa trẻ khác không?', 'is_red_flag' => true, 'flag_reason' => 'Không quan tâm hoặc né tránh trẻ cùng trang lứa.'],
                'q3' => ['title' => 'Bé có thích leo trèo lên các đồ vật, ví dụ như cầu thang hoặc đồ đạc không?', 'is_red_flag' => false],
                'q4' => ['title' => 'Bé có thích chơi trò chơi ú òa hoặc trốn tìm không?', 'is_red_flag' => false],
                'q5' => ['title' => 'Bé có bao giờ chơi giả vờ không (ví dụ: giả vờ uống nước từ cốc rỗng, giả vờ gọi điện thoại, hoặc đút thìa cho gấu bông/búp bê ăn)?', 'is_red_flag' => true, 'flag_reason' => 'Thiếu hụt kỹ năng chơi giả vờ (Pretend Play) - dấu hiệu chỉ báo nhận thức xã hội.'],
                'q6' => ['title' => 'Bé có bao giờ dùng ngón tay trỏ để chỉ vào đồ vật nhằm ĐÒI LẤY thứ gì đó không?', 'is_red_flag' => true, 'flag_reason' => 'Không biết dùng ngón trỏ để yêu cầu đồ vật mong muốn.'],
                'q7' => ['title' => 'Bé có bao giờ dùng ngón tay trỏ để chỉ vào đồ vật nhằm KHOE với bạn điều gì thú vị không (ví dụ: chỉ máy bay trên trời, chỉ con chó)?', 'is_red_flag' => true, 'flag_reason' => 'Thiếu hụt cử chỉ chỉ ngón trỏ chia sẻ chú ý (Proto-declarative pointing).'],
                'q8' => ['title' => 'Bé có bao giờ mang đồ vật lại gần bạn để cho bạn xem không (chỉ để khoe chứ không phải đòi bạn giúp)?', 'is_red_flag' => false],
                'q9' => ['title' => 'Bé có nhìn thẳng vào mắt bạn trong vài giây khi bạn nói chuyện hoặc chơi với bé không?', 'is_red_flag' => true, 'flag_reason' => 'Giao tiếp mắt kém hoặc không duy trì ánh nhìn.'],
                'q10' => ['title' => 'Bé có vẻ quá nhạy cảm với tiếng ồn (ví dụ: bịt hai tai hoặc khóc thét khi nghe tiếng máy sấy, máy hút bụi)?', 'is_red_flag' => false],
                'q11' => ['title' => 'Bé có mỉm cười đáp lại khuôn mặt hoặc nụ cười của bạn không?', 'is_red_flag' => true, 'flag_reason' => 'Không có nụ cười đáp lại giao tiếp xã hội.'],
                'q12' => ['title' => 'Bé có bắt chước những hành động của bạn không (ví dụ: làm mặt cười, vỗ tay, vẫy tay chào)?', 'is_red_flag' => false],
                'q13' => ['title' => 'Bé có quay đầu lại nhìn khi bạn gọi tên bé không?', 'is_red_flag' => true, 'flag_reason' => 'Không phản xạ quay đầu lại khi người lớn gọi tên.'],
                'q14' => ['title' => 'Khi bạn chỉ tay vào một món đồ chơi ở phía xa trong phòng, bé có nhìn theo hướng bạn chỉ không?', 'is_red_flag' => true, 'flag_reason' => 'Không nhìn theo hướng ngón tay chỉ của người lớn.'],
                'q15' => ['title' => 'Bé đã biết đi chưa?', 'is_red_flag' => false],
                'q16' => ['title' => 'Bé có nhìn vào những thứ mà bạn đang nhìn không?', 'is_red_flag' => false],
                'q17' => ['title' => 'Bé có làm những cử động ngón tay kỳ lạ gần mắt bé không (ví dụ: vẫy ngón tay trước mắt)?', 'is_red_flag' => true, 'flag_reason' => 'Cử động ngón tay dị thường trước mắt (Visual stimming).'],
                'q18' => ['title' => 'Bé có cố gắng thu hút sự chú ý của bạn vào hoạt động mà bé đang làm không?', 'is_red_flag' => false],
                'q19' => ['title' => 'Bạn có bao giờ tự hỏi liệu con mình có bị khiếm thính hoặc bị điếc không?', 'is_red_flag' => false],
                'q20' => ['title' => 'Bé có hiểu được những gì người khác nói với bé không (ví dụ: "lại đây", "đưa quả bóng cho mẹ")?', 'is_red_flag' => true, 'flag_reason' => 'Không hiểu các mệnh lệnh ngôn ngữ tiếp nhận cơ bản.'],
            ]
        ];
    }

    /**
     * Bộ câu hỏi 3: Khả Năng Hiểu Lệnh & Thính Lực (12 - 36 tháng)
     */
    private function getReceptiveQuestions(): array
    {
        return [
            'meta' => [
                'title' => 'Bảng Phân Tích Khả Năng Hiểu Lệnh & Thính Lực',
                'target_age' => 'Dành cho trẻ từ 12 đến 36 tháng tuổi',
                'badge' => 'Phân loại nguyên nhân',
                'desc' => 'Giúp xác định xem con chậm nói do cơ quan phát âm (chậm nói biểu đạt đơn thuần) hay do chưa hiểu ngôn ngữ tiếp nhận hoặc giảm thính lực.',
            ],
            'questions' => [
                'q1' => [
                    'category' => 'Ngôn ngữ tiếp nhận (Khả năng hiểu)',
                    'title' => 'Bé có hiểu và thực hiện đúng mệnh lệnh 1 bước đơn giản không cần cử chỉ phụ trợ (ví dụ: "Lại đây với mẹ", "Đưa bóng cho bố")?',
                    'desc' => 'Kiểm tra xem bé hiểu lời nói hay chỉ nhìn theo cử chỉ tay của bạn.',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Không hiểu và không làm theo mệnh lệnh 1 bước đơn giản.',
                ],
                'q2' => [
                    'category' => 'Ngôn ngữ tiếp nhận (Khả năng hiểu)',
                    'title' => 'Khi được hỏi "Mắt đâu?", "Mũi đâu?", "Tai đâu?", bé có biết chỉ đúng ít nhất 2-3 bộ phận trên cơ thể mình hoặc của mẹ không?',
                    'desc' => 'Khả năng định danh bộ phận cơ thể là chỉ số đo lường nhận thức ngôn ngữ sớm.',
                    'risk_answer' => 'no',
                    'is_red_flag' => false,
                ],
                'q3' => [
                    'category' => 'Ngôn ngữ tiếp nhận (Khả năng hiểu)',
                    'title' => 'Bé có nhận biết và lấy đúng các đồ vật quen thuộc hằng ngày khi được yêu cầu (ví dụ: đôi giày, bình sữa, cái thìa, cái lược)?',
                    'desc' => 'Vốn từ vựng thụ động (từ hiểu trong đầu trước khi nói ra miệng).',
                    'risk_answer' => 'no',
                    'is_red_flag' => false,
                ],
                'q4' => [
                    'category' => 'Thính lực & Phản xạ Âm thanh',
                    'title' => 'Khi có tiếng động bất ngờ phát ra từ phía sau lưng bé (tiếng gõ muỗng, tiếng rơi đồ, tiếng gọi nhỏ), bé có giật mình hoặc lập tức quay đầu tìm nguồn phát âm thanh không?',
                    'desc' => 'Phản xạ định hướng âm thanh (Sound Localization). Trẻ khiếm thính sẽ không có phản xạ này.',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Không có phản xạ quay đầu định hướng nguồn âm thanh từ phía sau lưng.',
                ],
                'q5' => [
                    'category' => 'Thính lực & Phản xạ Âm thanh',
                    'title' => 'Khi bạn nói thì thầm ở cự ly khoảng 1 mét sau lưng bé, bé có phản ứng quay lại nhìn không?',
                    'desc' => 'Kiểm tra mức ngưỡng nghe ở dải âm lượng nhỏ (whisper test).',
                    'risk_answer' => 'no',
                    'is_red_flag' => true,
                    'flag_reason' => 'Nghi ngờ giảm ngưỡng nghe ở âm lượng nhỏ.',
                ],
                'q6' => [
                    'category' => 'Ngôn ngữ tiếp nhận (Khả năng hiểu)',
                    'title' => 'Bé có hiểu từ "Không được / Đừng!" và dừng hành động lại khi nghe người lớn nhắc nhở nghiêm túc không?',
                    'desc' => 'Khả năng hiểu tín hiệu ngăn cấm và điều chỉnh hành vi theo lời nói.',
                    'risk_answer' => 'no',
                    'is_red_flag' => false,
                ],
                'q7' => [
                    'category' => 'Tiền sử Thính học & Bệnh lý Tai',
                    'title' => 'Bé có tiền sử viêm tai giữa tái phát nhiều lần, hoặc thường xuyên dùng tay ngoáy tai, kéo tai, lắc đầu không?',
                    'desc' => 'Viêm tai giữa ứ dịch mạn tính là nguyên nhân thầm lặng rất phổ biến khiến trẻ nghe không rõ âm thanh và dẫn đến chậm nói.',
                    'risk_answer' => 'yes',
                    'is_red_flag' => true,
                    'flag_reason' => 'Tiền sử viêm tai giữa tái phát hoặc có dấu hiệu khó chịu ở tai.',
                ],
                'q8' => [
                    'category' => 'Ngôn ngữ biểu đạt & Phát âm',
                    'title' => 'Bé hiểu hết mọi thứ cha mẹ nói, làm đúng 100% việc được nhờ, chỉ ngón trỏ tốt, nhưng miệng chỉ phát ra âm ê a hoặc chưa nói được từ có nghĩa?',
                    'desc' => 'Nếu bé hiểu rất tốt mọi thứ nhưng chưa phát âm được từ, khả năng rất cao là Bé thuộc nhóm CHẬM NÓI BIỂU ĐẠT ĐƠN THUẦN (tiên lượng phục hồi rất tốt).',
                    'risk_answer' => 'yes',
                    'is_red_flag' => false,
                ],
            ]
        ];
    }
}
