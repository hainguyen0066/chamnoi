<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FlashcardController extends Controller
{
    public function index()
    {
        $flashcards = [
            'dong_vat' => [
                'category_name' => 'Tiếng kêu con vật',
                'description' => 'Trẻ luôn tiếp thu âm thanh tượng thanh trước khi học từ vựng phức tạp.',
                'items' => [
                    ['word' => 'Gâu gâu', 'label' => 'Con chó', 'icon' => '🐶', 'tip' => 'Nhấn mạnh âm /g/ ở cuống họng, vỗ nhẹ ngực bé theo nhịp.', 'audio' => asset('audio/flashcards/gau-gau.mp3')],
                    ['word' => 'Meo meo', 'label' => 'Con mèo', 'icon' => '🐱', 'tip' => 'Kéo dài âm môi /m/, làm động tác vuốt râu mèo.', 'audio' => asset('audio/flashcards/meo-meo.mp3')],
                    ['word' => 'Ùm bòooo', 'label' => 'Con bò', 'icon' => '🐮', 'tip' => 'Ngậm môi phát âm /ùm/ rồi mở rộng miệng kêu /bòoo/.', 'audio' => asset('audio/flashcards/um-bo.mp3')],
                    ['word' => 'Cục tác', 'label' => 'Con gà mái', 'icon' => '🐔', 'tip' => 'Âm bật /c/ giúp rèn luyện cơ vòm họng.', 'audio' => asset('audio/flashcards/cuc-tac.mp3')],
                    ['word' => 'Chíp chíp', 'label' => 'Gà con', 'icon' => '🐥', 'tip' => 'Âm đầu lưỡi /ch/, nhọn mỏ như chú gà con mổ thóc.', 'audio' => asset('audio/flashcards/chip-chip.mp3')],
                    ['word' => 'Quạc quạc', 'label' => 'Con vịt', 'icon' => '🦆', 'tip' => 'Hai tay làm cánh vịt vỗ phành phạch tạo cảm xúc hào hứng.', 'audio' => asset('audio/flashcards/quac-quac.mp3')],
                ],
            ],
            'phuong_tien' => [
                'category_name' => 'Âm thanh xe cộ',
                'description' => 'Các âm rung môi và bật hơi mô phỏng thế giới giao thông.',
                'items' => [
                    ['word' => 'Brum brum', 'label' => 'Ô tô chạy', 'icon' => '🚗', 'tip' => 'Rung hai cánh môi liên tục để luồng hơi bật ra.', 'audio' => asset('audio/flashcards/brum-brum.mp3')],
                    ['word' => 'Bim bim', 'label' => 'Bấm còi xe', 'icon' => '📢', 'tip' => 'Âm môi /b/ dứt khoát kết hợp ấn nhẹ vào mũi con.', 'audio' => asset('audio/flashcards/bim-bim.mp3')],
                    ['word' => 'Xình xịch', 'label' => 'Đoàn tàu hỏa', 'icon' => '🚂', 'tip' => 'Hai khuỷu tay xoay tròn theo nhịp điệu xình xịch.', 'audio' => asset('audio/flashcards/xinh-xich.mp3')],
                    ['word' => 'Tu tu', 'label' => 'Còi tàu hỏa', 'icon' => '🚢', 'tip' => 'Chu tròn môi như huýt sáo để phát âm /tu/.', 'audio' => asset('audio/flashcards/tu-tu.mp3')],
                    ['word' => 'Ò e ó e', 'label' => 'Xe cứu thương', 'icon' => '🚑', 'tip' => 'Lên xuống cao độ của giọng nói giúp kích thích thính giác.', 'audio' => asset('audio/flashcards/o-e-o-e.mp3')],
                ],
            ],
            'nhu_cau' => [
                'category_name' => 'Từ ngữ nhu cầu cốt lõi',
                'description' => 'Những từ giúp con biểu đạt mong muốn ngay lập tức thay vì ăn vạ.',
                'items' => [
                    ['word' => 'Măm măm', 'label' => 'Đòi ăn cơm', 'icon' => '🥣', 'tip' => 'Hai môi mím chặt rồi mở ra, kèm động tác xoa bụng.', 'audio' => asset('audio/flashcards/mam-mam.mp3')],
                    ['word' => 'Bế', 'label' => 'Đòi bế lên', 'icon' => '🫂', 'tip' => 'Hai tay giơ lên cao, nói dứt khoát một âm /bế/.', 'audio' => asset('audio/flashcards/be.mp3')],
                    ['word' => 'Đi', 'label' => 'Đòi đi chơi', 'icon' => '👟', 'tip' => 'Chỉ tay về phía cửa ra vào và nhấc chân bước.', 'audio' => asset('audio/flashcards/di.mp3')],
                    ['word' => 'Mở', 'label' => 'Mở hộp / Mở cửa', 'icon' => '🚪', 'tip' => 'Xòe hai bàn tay ra mô phỏng động tác mở cánh cửa.', 'audio' => asset('audio/flashcards/mo.mp3')],
                    ['word' => 'Ạ', 'label' => 'Khoanh tay xin xỏ', 'icon' => '🙏', 'tip' => 'Khoanh hai tay trước ngực, cúi đầu kèm âm /ạ/ vang.', 'audio' => asset('audio/flashcards/a.mp3')],
                    ['word' => 'Hết', 'label' => 'Đã ăn xong / Hết sữa', 'icon' => '🥣', 'tip' => 'Ngửa hai bàn tay lắc lắc: "Hết rồi!".', 'audio' => asset('audio/flashcards/het.mp3')],
                ],
            ],
            'co_mieng' => [
                'category_name' => 'Vận động cơ miệng & Luồng hơi',
                'description' => 'Bài tập thể dục cho môi, lưỡi và luồng hơi thở.',
                'items' => [
                    ['word' => 'Phù phù', 'label' => 'Thổi tắt nến / Thổi nguội', 'icon' => '🎂', 'tip' => 'Chu môi thổi hơi làm bay ngọn nến hoặc thổi chong chóng.', 'audio' => asset('audio/flashcards/phu-phu.mp3')],
                    ['word' => 'Cốc cốc', 'label' => 'Tặc lưỡi ngựa phi', 'icon' => '🐴', 'tip' => 'Dính cuống lưỡi lên hàm ếch trên rồi giật xuống tạo tiếng gõ.', 'audio' => asset('audio/flashcards/coc-coc.mp3')],
                    ['word' => 'Chụt chụt', 'label' => 'Hôn gió', 'icon' => '💋', 'tip' => 'Mím chặt môi rồi bật ra tiếng hôn gió thật to.', 'audio' => asset('audio/flashcards/chut-chut.mp3')],
                    ['word' => 'Phồng má', 'label' => 'Con ếch phồng má', 'icon' => '🐸', 'tip' => 'Ngậm chặt miệng giữ hơi làm phồng 2 má rồi dùng tay ấn bụp.', 'audio' => asset('audio/flashcards/phong-ma.mp3')],
                ],
            ],
        ];

        return view('flashcards.index', compact('flashcards'));
    }
}
