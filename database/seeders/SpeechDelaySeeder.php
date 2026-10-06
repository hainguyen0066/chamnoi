<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ScreeningQuestion;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SpeechDelaySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cấu hình Website
        $settings = [
            ['key' => 'site_name', 'value' => 'Mầm Ngôn Ngữ - Cẩm Nang Chậm Nói Ở Trẻ', 'label' => 'Tên Website', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Hiểu đúng nguyên nhân, đồng hành khoa học cùng con bật âm mỗi ngày', 'label' => 'Khẩu hiệu (Slogan)', 'group' => 'general'],
            ['key' => 'hotline', 'value' => '1900 6868 (Miễn phí tư vấn tâm lý - ngôn ngữ)', 'label' => 'Hotline tư vấn', 'group' => 'contact'],
            ['key' => 'zalo_support', 'value' => '0988.123.456', 'label' => 'Zalo chuyên viên tư vấn', 'group' => 'contact'],
            ['key' => 'contact_email', 'value' => 'hotro@mamngonngu.vn', 'label' => 'Email hỗ trợ', 'group' => 'contact'],
            ['key' => 'community_group_url', 'value' => 'https://facebook.com/groups/donghanhcungconchamnoi', 'label' => 'Link nhóm Cộng đồng Phụ huynh', 'group' => 'social'],
            ['key' => 'medical_disclaimer', 'value' => 'Thông tin và bài trắc nghiệm trên website mang tính chất sàng lọc và tham khảo khoa học, không thay thế cho chẩn đoán y khoa chính thức của bác sĩ chuyên khoa Nhi hoặc Tâm lý - Âm ngữ trị liệu.', 'label' => 'Cảnh báo y khoa (Disclaimer)', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Bài viết / Cẩm nang chuyên sâu
        $articles = [
            [
                'title' => 'Tổng quan về chậm nói ở trẻ: Khái niệm, phân loại và tầm quan trọng của giai đoạn vàng',
                'slug' => 'tong-quan-ve-cham-noi-o-tre-giai-doan-vang',
                'category' => 'nguyen-nhan',
                'age_group' => 'all',
                'reading_time' => '6 phút đọc',
                'badge_text' => 'Kiến thức cốt lõi',
                'badge_color' => 'blue',
                'is_featured' => true,
                'order_index' => 1,
                'views_count' => 1420,
                'excerpt' => 'Giai đoạn từ 0 - 3 tuổi là "thời kỳ cửa sổ vàng" cho não bộ phát triển ngôn ngữ. Nhận biết sớm giúp con bắt kịp bạn bè đến 90%.',
                'content' => '
<h3>1. Thế nào là trẻ chậm nói?</h3>
<p>Chậm nói (Speech delay) là tình trạng khả năng phát triển ngôn ngữ của trẻ chậm hơn so với các mốc phát triển thông thường của trẻ cùng lứa tuổi. Chậm nói có thể chia thành hai khía cạnh chính:</p>
<ul>
    <li><strong>Chậm diễn đạt (Expressive language delay):</strong> Trẻ hiểu những gì người lớn nói, làm theo mệnh lệnh tốt nhưng không bật ra lời nói hoặc vốn từ rất ít.</li>
    <li><strong>Chậm tiếp nhận (Receptive language delay):</strong> Trẻ gặp khó khăn trong việc hiểu ngôn ngữ, không phản ứng với tên gọi, khó làm theo yêu cầu đơn giản.</li>
</ul>

<h3>2. Vì sao giai đoạn 0 – 3 tuổi lại gọi là "Cửa sổ vàng"?</h3>
<p>Trong 3 năm đầu đời, não bộ của trẻ hình thành hơn 1 triệu kết nối thần kinh mới mỗi giây. Đây là thời điểm não bộ có tính mềm dẻo cao nhất (neuroplasticity). Nếu can thiệp âm ngữ trị liệu và điều chỉnh môi trường giao tiếp trước 3 tuổi, tỷ lệ trẻ tiến bộ vượt bậc và hòa nhập bình thường đạt trên 85-90%.</p>

<div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-xl my-4 text-amber-900">
    <strong>Lời khuyên chuyên gia:</strong> Đừng bao giờ giữ quan niệm "Chờ con 3 tuổi tự biết nói" hay "Bố nó ngày xưa 4 tuổi mới biết nói". Sự chờ đợi thụ động có thể bỏ lỡ cơ hội can thiệp sớm tốt nhất của trẻ.
</div>
',
            ],
            [
                'title' => 'Bảng phân biệt chi tiết: Chậm nói đơn thuần vs Rối loạn phổ tự kỷ (ASD)',
                'slug' => 'phan-biet-cham-noi-don-thuan-va-tu-ky',
                'category' => 'phan-biet',
                'age_group' => '12-24m',
                'reading_time' => '8 phút đọc',
                'badge_text' => 'Phụ huynh quan tâm nhất',
                'badge_color' => 'rose',
                'is_featured' => true,
                'order_index' => 2,
                'views_count' => 3890,
                'excerpt' => 'Rất nhiều cha mẹ hoang mang lo sợ con bị tự kỷ khi thấy con chậm nói. Xem ngay 6 tiêu chí then chốt để phân biệt rạch ròi giữa hai tình trạng này.',
                'content' => '
<h3>Nỗi sợ lớn nhất của cha mẹ: "Con tôi chỉ chậm nói hay bị tự kỷ?"</h3>
<p>Không phải mọi trẻ chậm nói đều là tự kỷ. Điểm khác biệt quan trọng nhất không nằm ở số lượng từ ngữ con nói được, mà nằm ở <strong>NHU CẦU VÀ KHẢ NĂNG TƯƠNG TÁC XÃ HỘI (Social Communication)</strong>.</p>

<div class="overflow-x-auto my-6">
    <table class="w-full text-left border-collapse border border-slate-200">
        <thead>
            <tr class="bg-slate-100 text-slate-700">
                <th class="p-3 border border-slate-200 font-bold">Tiêu chí quan sát</th>
                <th class="p-3 border border-slate-200 font-bold text-emerald-700">Trẻ chậm nói đơn thuần</th>
                <th class="p-3 border border-slate-200 font-bold text-rose-700">Trẻ có dấu hiệu Tự kỷ (ASD)</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            <tr>
                <td class="p-3 border border-slate-200 font-semibold">Giao tiếp mắt (Eye contact)</td>
                <td class="p-3 border border-slate-200">Nhìn vào mắt cha mẹ chăm chú, thể hiện cảm xúc vui buồn rõ ràng.</td>
                <td class="p-3 border border-slate-200">Tránh né ánh mắt, nhìn lướt qua hoặc như nhìn xuyên qua người đối diện.</td>
            </tr>
            <tr>
                <td class="p-3 border border-slate-200 font-semibold">Cử chỉ chỉ tay (Pointing)</td>
                <td class="p-3 border border-slate-200">Dùng ngón tay trỏ chỉ vào món đồ muốn xin hoặc chỉ con chó, cái xe để khoe với mẹ.</td>
                <td class="p-3 border border-slate-200">Hiếm khi chỉ tay; thường cầm cả bàn tay người lớn đặt vào vật phẩm như một công cụ.</td>
            </tr>
            <tr>
                <td class="p-3 border border-slate-200 font-semibold">Phản ứng khi gọi tên</td>
                <td class="p-3 border border-slate-200">Quay đầu lại ngay khi ba mẹ gọi tên (trừ khi đang mải chơi quá tập trung).</td>
                <td class="p-3 border border-slate-200">Ít hoặc không quay lại dù gọi nhiều lần, có vẻ ngoài như bị khiếm thính.</td>
            </tr>
            <tr>
                <td class="p-3 border border-slate-200 font-semibold">Giao tiếp không lời (Body language)</td>
                <td class="p-3 border border-slate-200">Dùng điệu bộ, kéo tay, gật đầu, lắc đầu, mỉm cười để bù đắp cho lời nói.</td>
                <td class="p-3 border border-slate-200">Thiếu cử chỉ bù trừ, nét mặt ít biểu cảm tương thích hoàn cảnh.</td>
            </tr>
            <tr>
                <td class="p-3 border border-slate-200 font-semibold">Hành vi định hình / rập khuôn</td>
                <td class="p-3 border border-slate-200">Chơi đồ chơi đa dạng, biết chơi giả vờ (cho gấu ăn, bế búp bê).</td>
                <td class="p-3 border border-slate-200">Thích xoay bánh xe, xếp đồ chơi thành hàng dài, nhón gót, vẫy tay trước mắt.</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl my-4 text-emerald-900">
    <strong>Kết luận an tâm:</strong> Nếu con bạn chưa nói được nhiều từ nhưng <em>thích quấn quýt cha mẹ, biết chỉ tay khoe đồ, biết kéo tay rủ chơi, hiểu mệnh lệnh và cười đùa đáp ứng</em>, khả năng cao bé chỉ là chậm nói diễn đạt đơn thuần hoặc thiếu môi trường tương tác.
</div>
',
            ],
            [
                'title' => 'Tác hại khôn lường của màn hình Tivi, Smartphone đối với não bộ và giọng nói của trẻ',
                'slug' => 'tac-hai-cua-man-hinh-smartphone-tre-cham-noi',
                'category' => 'nguyen-nhan',
                'age_group' => '0-12m',
                'reading_time' => '5 phút đọc',
                'badge_text' => 'Báo động đỏ',
                'badge_color' => 'purple',
                'is_featured' => true,
                'order_index' => 3,
                'views_count' => 2540,
                'excerpt' => 'Tại sao cho con xem Youtube Kids, Baby Bus hay Cocomelon nhiều lại khiến con "nghiện thế giới ảo" và quên luôn phản xạ nói tiếng mẹ đẻ?',
                'content' => '
<h3>1. Cơ chế giao tiếp 1 chiều - Kẻ đánh cắp phản xạ ngôn ngữ</h3>
<p>Ngôn ngữ của con người phát triển dựa trên <strong>CƠ CHẾ GIAO TIẾP 2 CHIỀU (Phát tín hiệu - Nhận phản hồi)</strong>. Khi đứa trẻ bập bẹ "Ba", người cha cười và đáp lại "Ba đây!", não bộ trẻ nhận được phần thưởng dopamine và củng cố liên kết phản xạ.</p>
<p>Ngược lại, điện thoại và Tivi phát ra hình ảnh chuyển động cực nhanh với màu sắc bắt mắt nhưng là <strong>GIAO TIẾP 1 CHIỀU</strong>. Trẻ chỉ thụ động tiếp nhận hình ảnh và âm thanh mà không có nhu cầu biểu đạt lại. Lâu dần, vùng ngôn ngữ Broca và Wernicke ở vỏ não bị teo giảm kích thích hoạt động.</p>

<h3>2. Hội chứng "Ảo giác ngôn ngữ" (Nói nhại tiếng nước ngoài vô nghĩa)</h3>
<p>Nhiều phụ huynh tự hào khi con mới 2 tuổi đã đếm được tiếng Anh từ 1 đến 10, thuộc bảng chữ cái ABC tiếng Anh qua Youtube nhưng... <strong>gọi tên không quay lại, không biết gọi "Mẹ ơi con đói", không biết xin nước uống</strong>. Đây không phải là thần đồng, mà là biểu hiện của <em>tập nhiễm âm thanh thụ động (Echolalia)</em>, thiếu khả năng ứng dụng ngôn ngữ vào thực tế cuộc sống.</p>

<h3>3. Khuyến nghị cai thiết bị số theo Viện Hàn lâm Nhi khoa Hoa Kỳ (AAP):</h3>
<ul>
    <li><strong>Trẻ dưới 18 tháng:</strong> Tuyệt đối không tiếp xúc với màn hình điện tử (trừ video call với người thân).</li>
    <li><strong>Trẻ 18 - 24 tháng:</strong> Hạn chế tối đa, nếu xem phải có cha mẹ ngồi cùng giải thích nội dung.</li>
    <li><strong>Trẻ đang có dấu hiệu chậm nói:</strong> "Cai nghiện" màn hình 100% trong tối thiểu 30-60 ngày kết hợp tăng cường tương tác mặt đối mặt.</li>
</ul>
',
            ],
            [
                'title' => 'Nguyên tắc 3T và 5 bước xử lý vàng tại nhà khi phát hiện con chậm nói',
                'slug' => 'nguyen-tac-3t-va-5-buoc-xu-ly-tai-nha',
                'category' => 'cach-xu-ly',
                'age_group' => 'all',
                'reading_time' => '7 phút đọc',
                'badge_text' => 'Thực hành ngay',
                'badge_color' => 'emerald',
                'is_featured' => true,
                'order_index' => 4,
                'views_count' => 4120,
                'excerpt' => 'Công thức 3T kinh điển (Tắt thiết bị - Tắm ngôn ngữ - Tương tác mặt đối mặt) cùng kỹ thuật chờ đợi 5 giây kích hoạt con chủ động bật âm.',
                'content' => '
<h3>Nguyên tắc 3T cốt lõi dành cho mọi gia đình</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-4">
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
        <h4 class="font-bold text-rose-800 text-lg mb-2">1. TẮT THIẾT BỊ</h4>
        <p class="text-sm text-slate-700">Tắt tivi nền trong nhà, cất điện thoại, iPad xa tầm mắt trẻ. Tạo môi trường yên tĩnh để con lắng nghe tiếng nói con người thật.</p>
    </div>
    <div class="p-4 bg-sky-50 border border-sky-200 rounded-xl">
        <h4 class="font-bold text-sky-800 text-lg mb-2">2. TẮM NGÔN NGỮ</h4>
        <p class="text-sm text-slate-700">Mô tả mọi hành động hàng ngày: "Mẹ bóc chuối nhé", "Mẹ rót nước ấm này", "Bé mang giày màu đỏ". Nói chậm, từ ngữ rõ ràng, nhấn mạnh trọng âm.</p>
    </div>
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <h4 class="font-bold text-amber-800 text-lg mb-2">3. TƯƠNG TÁC MẶT ĐỐI MẶT</h4>
        <p class="text-sm text-slate-700">Ngồi xổm hoặc hạ thấp tầm mắt ngang bằng tầm mắt của con. Để con nhìn rõ khẩu hình miệng và biểu cảm gương mặt của cha mẹ khi nói.</p>
    </div>
</div>

<h3>Kỹ thuật "Quy tắc 5 giây chờ đợi" (Wait Time)</h3>
<p>Lỗi phổ biến nhất của cha mẹ là: <em>Quá chiều chuộng hoặc đoán trước ý con</em>. Bé vừa chỉ tay vào bình nước là mẹ đã vội mang nước tới; bé ê a là đã được đáp ứng ngay. Điều này vô tình triệt tiêu động lực mở miệng của trẻ!</p>
<p><strong>Cách thực hiện:</strong> Khi bé muốn đồ vật gì, hãy giữ món đồ gần miệng của bạn, nhìn vào mắt con và nói từ khóa (ví dụ: "Nước... Uống nước"). Sau đó <strong>giữ im lặng và chờ đợi kiên nhẫn trong 5 đến 7 giây</strong>. Khoảng lặng này tạo áp lực tích cực buộc não bộ trẻ phải tìm cách phát ra âm thanh.</p>

<h3>Quy tắc "Nói mẫu + 1" (Mở rộng từ ngữ)</h3>
<ul>
    <li>Nếu con chưa nói được từ nào: Cha mẹ chỉ nói từ đơn hoặc âm thanh tượng thanh ("Bóng", "Cá", "Gâu gâu", "Brum brum").</li>
    <li>Nếu con nói được 1 từ (Ví dụ: "Bóng"): Mẹ mở rộng thành 2 từ ("Bóng tròn", "Ném bóng").</li>
    <li>Nếu con nói 2 từ (Ví dụ: "Mẹ bế"): Mẹ mở rộng thành 3 từ ("Mẹ bế con nhé").</li>
</ul>
',
            ],
            [
                'title' => 'Top 8 trò chơi dân gian & kích hoạt âm thanh tại nhà không tốn một xu',
                'slug' => 'top-tro-choi-kich-am-tai-nha-cho-be',
                'category' => 'tro-choi',
                'age_group' => '12-24m',
                'reading_time' => '6 phút đọc',
                'badge_text' => 'Gợi ý vui nhộn',
                'badge_color' => 'amber',
                'is_featured' => false,
                'order_index' => 5,
                'views_count' => 1980,
                'excerpt' => 'Trẻ học nói nhanh nhất thông qua vui chơi cảm giác. Khám phá ngay các trò chơi vận động cơ miệng và giao tiếp mắt tự nhiên nhất.',
                'content' => '
<h3>1. Trò chơi Thổi bong bóng xà phòng (Kích thích cơ môi và cơ miệng)</h3>
<p>Thổi bong bóng giúp bé rèn luyện cơ vành môi, điều hòa hơi thở - nền tảng để phát âm các phụ âm môi như /b/, /m/, /p/.</p>
<p><strong>Cách chơi:</strong> Thổi bóng cho bay lơ lửng, sau đó giữ ống thổi trước miệng, nói "Bóng... Bùm!" và chờ con hào hứng bật ra âm "Bùm" hoặc "Bóng" rồi mới thổi tiếp.</p>

<h3>2. Trò chơi Ú oà và Trốn tìm gương (Tăng giao tiếp mắt)</h3>
<p>Dùng tấm khăn mỏng che mặt mẹ, nói "Mẹ đâu rồi ta?", đếm 1, 2, 3 và mở khăn ra hô to "Ú... OÀ!". Trò chơi này kích thích cảm xúc hồi hộp, bật cười và nhìn sâu vào mắt cha mẹ.</p>

<h3>3. Bắt chước âm thanh con vật và phương tiện (Onomatopoeia)</h3>
<p>Trẻ con thích âm thanh tượng thanh trước khi học từ vựng phức tạp:</p>
<ul>
    <li>Con bò: "Ùm bòooo" (kéo dài âm môi)</li>
    <li>Con chó: "Gâu gâu"</li>
    <li>Con mèo: "Meo meo"</li>
    <li>Ô tô: "Brum brum... Bim bim!"</li>
    <li>Tàu hỏa: "Xình xịch... Tu tu!"</li>
</ul>

<h3>4. Trò chơi Kéo cưa lừa xẻ / Chi chi chành chành</h3>
<p>Những bài đồng dao có nhịp điệu vần điệu giúp tai trẻ bắt nhịp âm tiết tiếng Việt cực kỳ hiệu quả. Khi đến đoạn cao trào "Bắt lấy con chim", bé sẽ vô cùng phấn khích và dễ bật cười thành tiếng.</p>
',
            ],
            [
                'title' => 'Dấu hiệu CỜ ĐỎ (Red Flags): Khi nào bắt buộc đưa con đi khám chuyên khoa ngay?',
                'slug' => 'dau-hieu-co-do-kham-chuyen-khoa-cham-noi',
                'category' => 'co-do',
                'age_group' => 'all',
                'reading_time' => '5 phút đọc',
                'badge_text' => 'Bác sĩ cảnh báo',
                'badge_color' => 'rose',
                'is_featured' => true,
                'order_index' => 6,
                'views_count' => 5120,
                'excerpt' => 'Nếu con xuất hiện bất kỳ dấu hiệu nào trong danh sách cờ đỏ dưới đây, cha mẹ không nên trì hoãn mà cần đặt lịch khám Nhi đồng hoặc Viện Tai Mũi Họng ngay.',
                'content' => '
<h3>Danh sách dấu hiệu cờ đỏ theo từng độ tuổi (Chuẩn CDC Hoa Kỳ)</h3>

<div class="space-y-4 my-4">
    <div class="p-4 bg-rose-50 border-l-4 border-rose-600 rounded-r-xl">
        <h4 class="font-bold text-rose-800">Dưới 12 tháng tuổi:</h4>
        <ul class="list-disc pl-5 text-sm text-slate-700 mt-1">
            <li>Không cười đáp lại nụ cười của cha mẹ lúc 6 tháng.</li>
            <li>Không bập bẹ các âm ba-ba, ma-ma, đa-đa lúc 9 tháng.</li>
            <li>Không có phản ứng giật mình trước âm thanh lớn (nguy cơ khiếm thính).</li>
        </ul>
    </div>

    <div class="p-4 bg-rose-50 border-l-4 border-rose-600 rounded-r-xl">
        <h4 class="font-bold text-rose-800">Từ 12 đến 18 tháng tuổi:</h4>
        <ul class="list-disc pl-5 text-sm text-slate-700 mt-1">
            <li>12 tháng: Không biết chỉ tay, không vẫy tay chào "bye bye", không lắc đầu khi không thích.</li>
            <li>15 tháng: Không nói được bất kỳ từ đơn có nghĩa nào.</li>
            <li>18 tháng: Vốn từ dưới 6 từ đơn, không hiểu các câu mệnh lệnh đơn giản như "Lấy bóng cho mẹ", "Lại đây".</li>
        </ul>
    </div>

    <div class="p-4 bg-rose-50 border-l-4 border-rose-600 rounded-r-xl">
        <h4 class="font-bold text-rose-800">Từ 24 đến 36 tháng tuổi:</h4>
        <ul class="list-disc pl-5 text-sm text-slate-700 mt-1">
            <li>24 tháng: Chưa tự ghép được 2 từ đơn với nhau (ví dụ: "Uống sữa", "Mẹ bế"), chỉ nhại lại lời người khác như con vẹt mà không hiểu nghĩa.</li>
            <li>36 tháng: Người ngoài nghe không hiểu bé nói gì; không biết dùng câu 3 từ; không biết đặt câu hỏi "Cái gì đây?".</li>
        </ul>
    </div>

    <div class="p-4 bg-red-100 border-l-4 border-red-700 rounded-r-xl">
        <h4 class="font-bold text-red-900">DẤU HIỆU ĐẶC BIỆT NGUY HIỂM - THOÁI LUI KỸ NĂNG (Regression):</h4>
        <p class="text-sm text-red-800 mt-1">Trẻ từng biết nói vài từ, biết vẫy tay nhưng bỗng nhiên <strong>QUÊN HẾT, KHÔNG NÓI NỮA, THU MÌNH LẠI</strong> ở bất kỳ lứa tuổi nào. Đây là dấu hiệu cần đưa đi khám chuyên khoa Tâm thần nhi / Thần kinh ngay lập tức.</p>
    </div>
</div>
',
            ],
            [
                'title' => 'Dính thắng lưỡi ở trẻ: Sự thật và những hiểu lầm khiến cha mẹ hoang mang',
                'slug' => 'dinh-thang-luoi-o-tre-su-that-va-hieu-lam',
                'category' => 'nguyen-nhan',
                'age_group' => '0-12m',
                'reading_time' => '5 phút đọc',
                'badge_text' => 'Giải mã hiểu lầm',
                'badge_color' => 'blue',
                'is_featured' => false,
                'order_index' => 7,
                'views_count' => 1730,
                'excerpt' => 'Dính thắng lưỡi có làm bé câm nín không nói được không? Có nên vội vàng đưa con đi cắt thắng lưỡi để "con nhanh biết nói"?',
                'content' => '
<h3>1. Dính thắng lưỡi là gì?</h3>
<p>Dính thắng lưỡi (Ankyloglossia) là một dị tật bẩm sinh nhẹ, trong đó màng niêm mạc dưới lưỡi ngắn hoặc dày khiến đầu lưỡi bị hạn chế cử động, khi thè lưỡi đầu lưỡi có hình trái tim hoặc khuyết chữ V.</p>

<h3>2. Hiểu lầm tai hại: "Dính thắng lưỡi gây CHẬM NÓI"</h3>
<p><strong>SỰ THẬT Y KHOA:</strong> Dính thắng lưỡi <strong>KHÔNG GÂY RA TÌNH TRẠNG CHẬM NÓI</strong>. Trung tâm ngôn ngữ nằm ở não bộ, còn lưỡi chỉ là cơ quan phát âm cơ học.</p>
<ul>
    <li>Dính thắng lưỡi chỉ ảnh hưởng đến việc <em>bú mẹ ở trẻ sơ sinh</em> và có thể gây <em>ngọng một số âm cần uốn cong đầu lưỡi</em> (như âm /r/, /l/, /tr/) khi trẻ đã lớn.</li>
    <li>Một đứa trẻ bị dính thắng lưỡi vẫn có thể nói rất nhiều từ khác (ba, mẹ, bà, đi, cá...). Nếu trẻ hoàn toàn không nói từ nào, nguyên nhân chắc chắn nằm ở môi trường, nhận thức hoặc thính lực chứ không phải do thắng lưỡi!</li>
</ul>

<div class="p-4 bg-sky-50 border-l-4 border-sky-600 rounded-r-xl my-4 text-sky-900">
    <strong>Lời khuyên:</strong> Hãy đưa bé đi khám Tai Mũi Họng để bác sĩ đánh giá độ dính (độ 1, 2, 3, 4). Tuyệt đối không tự ý cắt thắng lưỡi với hy vọng "cắt xong con sẽ tự động biết nói".
</div>
',
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 3. Bộ câu hỏi sàng lọc theo độ tuổi (Screening Questions)
        $questions = [
            // Nhóm 12-18 tháng
            [
                'age_group' => '12-18m',
                'question' => 'Bé có phản ứng quay đầu lại ngay khi bạn gọi tên bé không?',
                'explanation' => 'Phản ứng với tên gọi chứng tỏ thính lực của bé bình thường và bé nhận thức được bản thân.',
                'category' => 'tuong_tac',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 1,
            ],
            [
                'age_group' => '12-18m',
                'question' => 'Bé có biết dùng ngón tay trỏ để chỉ vào món đồ chơi hoặc đồ vật bé muốn không?',
                'explanation' => 'Chỉ tay là cột mốc giao tiếp không lời quan trọng nhất trước khi trẻ biết nói.',
                'category' => 'ngon_ngu',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 2,
            ],
            [
                'age_group' => '12-18m',
                'question' => 'Bé có giao tiếp mắt tốt khi bạn nói chuyện, mỉm cười hoặc chơi ú oà cùng bé không?',
                'explanation' => 'Giao tiếp mắt là nền tảng để trẻ bắt chước biểu cảm và khẩu hình phát âm.',
                'category' => 'giao_tiep_mat',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 3,
            ],
            [
                'age_group' => '12-18m',
                'question' => 'Bé có biết bắt chước các cử chỉ đơn giản như: vẫy tay chào "bye bye", vỗ tay hoan hô, lắc đầu không?',
                'explanation' => 'Khả năng bắt chước vận động là tiền đề cho bắt chước âm thanh.',
                'category' => 'tuong_tac',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 4,
            ],
            [
                'age_group' => '12-18m',
                'question' => 'Bé đã nói được ít nhất 1 đến 3 từ đơn có nghĩa (như: ba, mẹ, bà, măm, đi)?',
                'explanation' => 'Từ 12-15 tháng bé thường bắt đầu xuất hiện những từ đơn đầu tiên.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 5,
            ],
            [
                'age_group' => '12-18m',
                'question' => 'Bé có hiểu và làm theo mệnh lệnh đơn giản kèm điệu bộ (ví dụ: "Đưa bóng cho mẹ", "Lại đây") không?',
                'explanation' => 'Khả năng tiếp nhận ngôn ngữ thường đi trước khả năng phát âm.',
                'category' => 'ngon_ngu',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 6,
            ],

            // Nhóm 18-24 tháng
            [
                'age_group' => '18-24m',
                'question' => 'Vốn từ của bé hiện tại có đạt từ 10 đến 20 từ đơn trở lên không?',
                'explanation' => 'Mốc 18-24 tháng là giai đoạn bùng nổ từ vựng, trẻ thường đạt từ 20-50 từ.',
                'category' => 'ngon_ngu',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 1,
            ],
            [
                'age_group' => '18-24m',
                'question' => 'Bé có tự ghép được cụm 2 từ (như: "Mẹ bế", "Uống sữa", "Xe đi", "Bố ơi") không?',
                'explanation' => 'Ghép 2 từ chứng tỏ trẻ đã bắt đầu tư duy ngữ pháp cơ bản.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 2,
            ],
            [
                'age_group' => '18-24m',
                'question' => 'Khi thấy con chó ngoài đường hoặc máy bay trên trời, bé có chỉ tay và nhìn sang bạn để rủ bạn cùng xem không?',
                'explanation' => 'Đây là "chú ý chung" (Joint attention) - chỉ số tương tác xã hội then chốt.',
                'category' => 'tuong_tac',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 3,
            ],
            [
                'age_group' => '18-24m',
                'question' => 'Bé có biết chỉ đúng vào các bộ phận trên cơ thể khi bạn hỏi (Mắt đâu, mũi đâu, tai đâu)?',
                'explanation' => 'Đánh giá khả năng hiểu từ vựng chỉ định của trẻ.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 4,
            ],
            [
                'age_group' => '18-24m',
                'question' => 'Bé có biểu hiện thích xem điện thoại/iPad liên tục nhiều giờ và cáu gắt dữ dội khi bị lấy lại không?',
                'explanation' => 'Nghiện màn hình là nguyên nhân hàng đầu gây hội chứng chậm nói giả.',
                'category' => 'hanh_vi',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1, // Yes ở đây là rủi ro
                'order_index' => 5,
            ],
            [
                'age_group' => '18-24m',
                'question' => 'Bé có hành vi lặp đi lặp lại như: xoay bánh xe, xếp đồ chơi thành hàng dài, nhón chân đi, vẫy bàn tay?',
                'explanation' => 'Các hành vi định hình cần lưu ý để phân biệt với phổ tự kỷ.',
                'category' => 'hanh_vi',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 6,
            ],

            // Nhóm 2-3 tuổi (24-36 tháng)
            [
                'age_group' => '2-3y',
                'question' => 'Bé có nói được câu ngắn 3-4 từ hoàn chỉnh (ví dụ: "Con muốn ăn cơm", "Bố đi làm về") không?',
                'explanation' => 'Ở tuổi lên 3, trẻ bình thường có thể nói câu đủ chủ ngữ - vị ngữ.',
                'category' => 'ngon_ngu',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 1,
            ],
            [
                'age_group' => '2-3y',
                'question' => 'Bé có hay đặt câu hỏi "Cái gì đây?", "Ai đấy?" hoặc trả lời được khi bạn hỏi tên bé không?',
                'explanation' => 'Tò mò và đặt câu hỏi thể hiện sự phát triển nhận thức và tương tác cao.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 2,
            ],
            [
                'age_group' => '2-3y',
                'question' => 'Người lạ hoặc người ít gặp có thể hiểu được khoảng 50% - 75% những gì bé nói không?',
                'explanation' => 'Độ rõ ràng của phát âm ở tuổi 2-3 giúp sàng lọc các vấn đề cơ quan phát âm.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 3,
            ],
            [
                'age_group' => '2-3y',
                'question' => 'Bé có biết chơi trò đóng vai giả vờ (cho gấu bông ăn, lái xe giả vờ, ru búp bê ngủ) không?',
                'explanation' => 'Trò chơi giả vờ (Pretend play) là bước phát triển trí tuệ biểu tượng rất quan trọng.',
                'category' => 'tuong_tac',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 4,
            ],
            [
                'age_group' => '2-3y',
                'question' => 'Bé có phản ứng thích chơi cùng bạn cùng lứa hay chỉ thích chơi một mình một góc?',
                'explanation' => 'Khả năng hòa nhập xã hội với bạn bè cùng trang lứa.',
                'category' => 'tuong_tac',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 5,
            ],

            // Nhóm 3-5 tuổi
            [
                'age_group' => '3-5y',
                'question' => 'Bé có kể lại được một mẩu chuyện ngắn, bài hát ngắn hoặc kể chuyện ở lớp mẫu giáo cho ba mẹ nghe không?',
                'explanation' => 'Trẻ 3-5 tuổi có thể thuật lại chuỗi sự việc có đầu có đuôi.',
                'category' => 'ngon_ngu',
                'is_red_flag' => true,
                'points_yes' => 0,
                'points_no' => 2,
                'order_index' => 1,
            ],
            [
                'age_group' => '3-5y',
                'question' => 'Bé có biết sử dụng đúng các đại từ nhân xưng: con, mẹ, bố, bạn, cô giáo không?',
                'explanation' => 'Sử dụng đại từ đúng thể hiện khả năng định vị cái tôi trong giao tiếp.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 2,
            ],
            [
                'age_group' => '3-5y',
                'question' => 'Lời nói của bé có trôi chảy không, hay thường xuyên bị vấp, giật cục, lặp từ lặp âm kéo dài (dấu hiệu nói lắp)?',
                'explanation' => 'Sàng lọc tình trạng nói lắp phát triển hoặc tăng động giảm chú ý.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 3,
            ],
            [
                'age_group' => '3-5y',
                'question' => 'Bé có hiểu các khái niệm trừu tượng hơn như: to/nhỏ, nhiều/ít, trước/sau, trên/dưới không?',
                'explanation' => 'Nhận thức không gian và số lượng liên kết với tư duy ngôn ngữ logic.',
                'category' => 'ngon_ngu',
                'is_red_flag' => false,
                'points_yes' => 0,
                'points_no' => 1,
                'order_index' => 4,
            ],
        ];

        foreach ($questions as $q) {
            ScreeningQuestion::updateOrCreate(
                ['age_group' => $q['age_group'], 'question' => $q['question']],
                $q
            );
        }
    }
}
