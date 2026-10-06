<?php

namespace App\Http\Controllers;

use App\Models\ScreeningQuestion;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index()
    {
        $ageMilestones = [
            '9m' => [
                'title' => 'Trẻ 9 tháng tuổi',
                'speech' => 'Bập bẹ các chuỗi âm thanh lặp lại: ba-ba, ma-ma, đa-đa; bắt đầu nhận biết tên gọi của mình.',
                'social' => 'Cười đáp lại, mỉm cười khi nhìn người thân, biết lạ người quen và sợ người lạ.',
                'red_flags' => ['Không bập bẹ âm thanh nào', 'Không quay đầu khi có tiếng động lớn', 'Không giao tiếp mắt khi bú hoặc chơi'],
            ],
            '12m' => [
                'title' => 'Trẻ 12 tháng tuổi (1 tuổi)',
                'speech' => 'Nói được 1-2 từ đơn có nghĩa (Ba, Mẹ, Bà); bắt chước âm thanh con vật.',
                'social' => 'Biết vẫy tay chào tạm biệt "bye-bye", biết lắc đầu khi không thích, biết chỉ tay vào món đồ muốn xin.',
                'red_flags' => ['Không biết chỉ tay', 'Không bắt chước cử chỉ (vẫy tay, vỗ tay)', 'Gọi tên không có phản ứng'],
            ],
            '18m' => [
                'title' => 'Trẻ 18 tháng tuổi (1.5 tuổi)',
                'speech' => 'Vốn từ từ 10 đến 20 từ đơn; hiểu và làm theo khẩu lệnh 1 bước đơn giản.',
                'social' => 'Biết chỉ đồ vật hoặc con vật để rủ cha mẹ cùng nhìn (Chú ý chung); thích chơi kéo tay rủ chơi.',
                'red_flags' => ['Vốn từ dưới 6 từ đơn', 'Không hiểu khẩu lệnh "lại đây", "đưa bóng"', 'Không biết chỉ vào bộ phận cơ thể'],
            ],
            '24m' => [
                'title' => 'Trẻ 24 tháng tuổi (2 tuổi)',
                'speech' => 'Vốn từ đạt 50 từ trở lên; bắt đầu tự ghép cụm 2 từ (Mẹ bế, uống sữa, xe đi).',
                'social' => 'Biết chơi giả vờ (cho gấu ăn, lái xe), bắt chước hành động của người lớn (quét nhà, bấm điện thoại).',
                'red_flags' => ['Chưa ghép được cụm 2 từ', 'Chỉ nhại lại lời như con vẹt mà không hiểu nghĩa', 'Không biết chơi đóng vai'],
            ],
            '36m' => [
                'title' => 'Trẻ 36 tháng tuổi (3 tuổi)',
                'speech' => 'Nói câu 3-4 từ hoàn chỉnh; hay đặt câu hỏi "Cái gì đây?"; người lạ hiểu được khoảng 75% lời nói của bé.',
                'social' => 'Biết chơi cùng bạn bè, thể hiện nhiều cung bậc cảm xúc, biết tên và tuổi của mình.',
                'red_flags' => ['Nói ngọng người ngoài không hiểu gì', 'Không nói được câu 3 từ', 'Tránh né bạn bè, chỉ chơi một mình một góc'],
            ],
        ];

        return view('milestones.index', compact('ageMilestones'));
    }
}
