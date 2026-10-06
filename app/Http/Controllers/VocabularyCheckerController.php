<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VocabularyCheckerController extends Controller
{
    public function index()
    {
        $categories = [
            'family_needs' => [
                'name' => 'Người thân & Nhu cầu sinh hoạt',
                'icon' => '👨‍👩‍👦',
                'words' => [
                    ['word' => 'Ba / Bố', 'icon' => '👨', 'level' => '12m'],
                    ['word' => 'Mẹ', 'icon' => '👩', 'level' => '12m'],
                    ['word' => 'Bà', 'icon' => '👵', 'level' => '12m'],
                    ['word' => 'Ông', 'icon' => '👴', 'level' => '12m'],
                    ['word' => 'Măm / Ăn', 'icon' => '🥣', 'level' => '12m'],
                    ['word' => 'Uống / Nước', 'icon' => '🥛', 'level' => '18m'],
                    ['word' => 'Bế', 'icon' => '🫂', 'level' => '12m'],
                    ['word' => 'Đi / Đi chơi', 'icon' => '👟', 'level' => '18m'],
                    ['word' => 'Ạ / Cảm ơn', 'icon' => '🙏', 'level' => '12m'],
                    ['word' => 'Không / Hông', 'icon' => '🙅', 'level' => '18m'],
                    ['word' => 'Mở / Mở ra', 'icon' => '🚪', 'level' => '18m'],
                    ['word' => 'Hết / Hết rồi', 'icon' => '✨', 'level' => '18m'],
                    ['word' => 'Cho / Xin', 'icon' => '🤲', 'level' => '18m'],
                    ['word' => 'Nữa / Thêm', 'icon' => '➕', 'level' => '24m'],
                ],
            ],
            'animals_sounds' => [
                'name' => 'Con vật & Tiếng tượng thanh',
                'icon' => '🐶',
                'words' => [
                    ['word' => 'Chó (Gâu gâu)', 'icon' => '🐕', 'level' => '12m'],
                    ['word' => 'Mèo (Meo meo)', 'icon' => '🐈', 'level' => '12m'],
                    ['word' => 'Gà (Cục tác / Chíp)', 'icon' => '🐔', 'level' => '18m'],
                    ['word' => 'Bò (Ùm bò)', 'icon' => '🐄', 'level' => '18m'],
                    ['word' => 'Vịt (Quạc quạc)', 'icon' => '🦆', 'level' => '18m'],
                    ['word' => 'Cá', 'icon' => '🐟', 'level' => '18m'],
                    ['word' => 'Chim', 'icon' => '🐦', 'level' => '24m'],
                    ['word' => 'Heo / Lợn (Ụt ịt)', 'icon' => '🐖', 'level' => '18m'],
                ],
            ],
            'objects_vehicles' => [
                'name' => 'Đồ vật & Phương tiện',
                'icon' => '🚗',
                'words' => [
                    ['word' => 'Xe / Ô tô (Brum)', 'icon' => '🚘', 'level' => '18m'],
                    ['word' => 'Bóng', 'icon' => '⚽', 'level' => '18m'],
                    ['word' => 'Quạt', 'icon' => '🪭', 'level' => '18m'],
                    ['word' => 'Đèn', 'icon' => '💡', 'level' => '18m'],
                    ['word' => 'Dép / Giày', 'icon' => '🩴', 'level' => '18m'],
                    ['word' => 'Thìa / Muỗng', 'icon' => '🥄', 'level' => '24m'],
                    ['word' => 'Sách / Truyện', 'icon' => '📖', 'level' => '24m'],
                    ['word' => 'Điện thoại', 'icon' => '📱', 'level' => '24m'],
                    ['word' => 'Tàu hỏa (Xình xịch)', 'icon' => '🚂', 'level' => '24m'],
                    ['word' => 'Máy bay (Ù ù)', 'icon' => '✈️', 'level' => '24m'],
                ],
            ],
            'actions_emotions' => [
                'name' => 'Hành động & Cảm xúc',
                'icon' => '🏃',
                'words' => [
                    ['word' => 'Ngủ', 'icon' => '😴', 'level' => '18m'],
                    ['word' => 'Tắm', 'icon' => '🛁', 'level' => '18m'],
                    ['word' => 'Tạm biệt / Bye', 'icon' => '👋', 'level' => '12m'],
                    ['word' => 'Nhìn / Xem', 'icon' => '👀', 'level' => '18m'],
                    ['word' => 'Cười / Vui', 'icon' => '😄', 'level' => '24m'],
                    ['word' => 'Đau / Éo', 'icon' => '🩹', 'level' => '18m'],
                    ['word' => 'Thơm / Hôn (Chụt)', 'icon' => '💋', 'level' => '18m'],
                    ['word' => 'Chạy / Nhảy', 'icon' => '🏃', 'level' => '24m'],
                    ['word' => 'Mát / Nóng', 'icon' => '🔥', 'level' => '24m'],
                    ['word' => 'Yêu / Thương', 'icon' => '❤️', 'level' => '24m'],
                ],
            ],
        ];

        $benchmarks = [
            '12m' => [
                'label' => '12 - 15 tháng',
                'expected_words' => '3 - 5 từ đơn',
                'safe_min' => 2,
                'ideal' => 6,
                'description' => 'Bé bắt đầu bật những âm đơn giản đầu tiên gắn liền với người thân thiết nhất.',
            ],
            '18m' => [
                'label' => '18 tháng',
                'expected_words' => '10 - 20 từ đơn',
                'safe_min' => 10,
                'ideal' => 20,
                'description' => 'Mốc ranh giới quan trọng. Nếu bé dưới 6 từ đơn ở mốc này, cần tăng cường tương tác mặt đối mặt ngay.',
            ],
            '24m' => [
                'label' => '24 tháng (2 tuổi)',
                'expected_words' => '50+ từ & ghép câu 2 từ',
                'safe_min' => 30,
                'ideal' => 50,
                'description' => 'Giai đoạn bùng nổ ngôn ngữ. Bé bắt đầu ghép 2 từ như: "Mẹ bế", "Uống nước", "Đi chơi".',
            ],
            '36m' => [
                'label' => '36 tháng (3 tuổi)',
                'expected_words' => '200+ từ & nói câu trọn vẹn',
                'safe_min' => 100,
                'ideal' => 200,
                'description' => 'Bé sử dụng đại từ nhân xưng (con, mẹ), biết đặt câu hỏi "Cái gì đây?" và kể chuyện ngắn.',
            ],
        ];

        $spinActivities = [
            [
                'title' => 'Trò chơi Thổi Bong Bóng Xà Phòng',
                'duration' => '5 phút',
                'goal' => 'Kích thích cơ môi chu tròn và tạo tình huống con phải nói "Thổi / Nữa" để mẹ thổi tiếp.',
                'tag' => 'Kích âm bật hơi',
                'icon' => '🫧',
            ],
            [
                'title' => 'Ú Òa Sau Khăn Lụa',
                'duration' => '7 phút',
                'goal' => 'Duy trì giao tiếp mắt tuyệt đối. Mẹ đếm "Một... hai..." và đợi con nói "Ba" hoặc bật cười mới giở khăn ra.',
                'tag' => 'Giao tiếp mắt',
                'icon' => '🙈',
            ],
            [
                'title' => 'Đóng Giả Chú Cún Đói Bụng',
                'duration' => '5 phút',
                'goal' => 'Mẹ cầm đồ chơi giả vờ làm cún kêu "Gâu gâu, đói quá", kích thích bé nhái lại âm tượng thanh dễ phát âm.',
                'tag' => 'Âm tượng thanh',
                'icon' => '🐶',
            ],
            [
                'title' => 'Cuộc Đua Tặc Lưỡi Ngựa Phi',
                'duration' => '3 phút',
                'goal' => 'Mẹ và bé cùng tặc lưỡi "Cốc cốc cốc", rèn luyện sự linh hoạt của cơ vòm họng và đầu lưỡi.',
                'tag' => 'Luyện cơ miệng',
                'icon' => '🐴',
            ],
            [
                'title' => 'Chiếc Hộp Bí Mật & Câu "Mở Ra"',
                'duration' => '5 phút',
                'goal' => 'Bỏ món đồ chơi bé thích vào hộp kín. Đợi bé nhìn mắt mẹ và nói từ khóa "Mở" hoặc "Giúp" mới mở ra.',
                'tag' => 'Biểu đạt nhu cầu',
                'icon' => '🎁',
            ],
            [
                'title' => 'Đọc Sách Ehon Bằng Giọng Điệu Hài Hước',
                'duration' => '10 phút',
                'goal' => 'Không đọc từng chữ khô khan. Dùng giọng trầm bổng, chỉ tay vào tranh và phát ra âm thanh vui nhộn.',
                'tag' => 'Vốn từ trực quan',
                'icon' => '📚',
            ],
        ];

        return view('vocabulary.index', compact('categories', 'benchmarks', 'spinActivities'));
    }
}
