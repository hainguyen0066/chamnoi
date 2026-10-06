@extends('layouts.app')

@section('title', 'Bé Biết Nói Bao Nhiêu Từ? Kiểm Tra Vốn Từ Đầu Đời Chuẩn CDC - Mầm Ngôn Ngữ')
@section('meta_description', 'Công cụ tương tác kiểm tra vốn từ vựng đầu đời của bé theo chuẩn mốc phát triển 12, 18, 24, 36 tháng tuổi. Tự động chấm điểm và gợi ý bài tập kích âm.')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16" x-data="{
    selectedAge: '18m',
    selectedWords: JSON.parse(localStorage.getItem('speech_child_words') || '[]'),
    activeTab: 'words',
    benchmarks: @js($benchmarks),
    spunActivity: null,
    isSpinning: false,

    toggleWord(word) {
        if (this.selectedWords.includes(word)) {
            this.selectedWords = this.selectedWords.filter(w => w !== word);
        } else {
            this.selectedWords.push(word);
        }
        localStorage.setItem('speech_child_words', JSON.stringify(this.selectedWords));
    },

    isWordSelected(word) {
        return this.selectedWords.includes(word);
    },

    clearAll() {
        if (confirm('Bạn có chắc muốn chọn lại từ đầu không?')) {
            this.selectedWords = [];
            localStorage.removeItem('speech_child_words');
        }
    },

    get totalCount() {
        return this.selectedWords.length;
    },

    get currentBenchmark() {
        return this.benchmarks[this.selectedAge] || this.benchmarks['18m'];
    },

    get evaluationStatus() {
        const count = this.totalCount;
        const bm = this.currentBenchmark;

        if (this.selectedAge === '12m') {
            if (count >= 5) return { label: 'Phát triển vượt trội 🌟', color: 'emerald', message: 'Vốn từ rất phong phú so với lứa tuổi 12-15 tháng!' };
            if (count >= 2) return { label: 'Đạt mốc chuẩn ✅', color: 'teal', message: 'Bé bắt đầu phát triển ngôn ngữ đúng lộ trình sinh lý bình thường.' };
            return { label: 'Cần tăng cường kích âm ⚠️', color: 'amber', message: 'Mẹ nên nói chậm, gọi tên con nhiều hơn và chơi ú òa mỗi ngày.' };
        } else if (this.selectedAge === '18m') {
            if (count >= 20) return { label: 'Rất xuất sắc 🎉', color: 'emerald', message: 'Bé sẵn sàng bước vào giai đoạn bùng nổ câu ghép 2 từ.' };
            if (count >= 10) return { label: 'Đạt chuẩn mốc 18 tháng ✅', color: 'teal', message: 'Vốn từ đạt ngưỡng an toàn theo tiêu chuẩn phát triển của CDC.' };
            if (count >= 6) return { label: 'Theo dõi sát & Kích âm ⚠️', color: 'amber', message: 'Vốn từ hơi chậm, cần cắt hoàn toàn tivi/điện thoại và tập quy tắc 3T.' };
            return { label: 'Cảnh báo chậm nói 🚩', color: 'rose', message: 'Dưới 6 từ ở 18 tháng là dấu hiệu cần quan sát cờ đỏ và can thiệp tích cực.' };
        } else if (this.selectedAge === '24m') {
            if (count >= 40) return { label: 'Ngôn ngữ rất phong phú 🚀', color: 'emerald', message: 'Bé có vốn từ dồi dào, hãy kích thích bé nói câu 3-4 từ.' };
            if (count >= 25) return { label: 'Đạt mức an toàn ✅', color: 'teal', message: 'Khuyến khích ghép từ: Ba đi, Mẹ bế, Con ăn...' };
            return { label: 'Có nguy cơ chậm nói 🚩', color: 'rose', message: 'Trẻ 2 tuổi cần đạt tối thiểu 20-30 từ. Hãy thực hiện ngay Lộ trình 30 ngày!' };
        } else {
            if (count >= 35) return { label: 'Đạt chuẩn phát triển ✅', color: 'emerald', message: 'Bé ghi nhớ tốt các từ ngữ sinh hoạt và giao tiếp.' };
            return { label: 'Cần can thiệp tích cực 🚩', color: 'rose', message: 'Bé cần được kích thích giao tiếp tích cực tại nhà mỗi ngày.' };
        }
    },

    spinWheel() {
        if (this.isSpinning) return;
        this.isSpinning = true;
        this.spunActivity = null;
        const activities = @js($spinActivities);

        let counter = 0;
        const interval = setInterval(() => {
            this.spunActivity = activities[Math.floor(Math.random() * activities.length)];
            counter++;
            if (counter > 15) {
                clearInterval(interval);
                this.isSpinning = false;
            }
        }, 100);
    },

    shareFacebook() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent('Con mình ' + this.currentBenchmark.label + ' đã nói được ' + this.totalCount + ' từ! Các mẹ thử kiểm tra vốn từ cho con xem:');
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + url + '&quote=' + text, '_blank');
    },

    copyShareLink() {
        navigator.clipboard.writeText(window.location.href);
        alert('Đã copy đường link! Bạn có thể dán vào Zalo hoặc nhóm Facebook của mẹ bỉm.');
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Kiểm tra vốn từ của con</span>
        </nav>

        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Thước đo vốn từ đầu đời
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Bé Biết Nói Bao Nhiêu Từ?
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Đánh dấu các từ bé <strong>đã từng tự nói hoặc nhại lại</strong> để đối chiếu với bảng mốc chuẩn quốc tế của CDC.
            </p>
        </div>

        <!-- Sticky Dashboard Widget -->
        <div class="sticky top-20 z-30 bg-white/95 backdrop-blur-md rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-200 mb-10 transition">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                
                <!-- Age selector -->
                <div class="w-full lg:w-auto">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">1. Chọn tháng tuổi của bé:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($benchmarks as $key => $bm)
                            <button @click="selectedAge = '{{ $key }}'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition flex flex-col items-center justify-center border"
                                    :class="selectedAge === '{{ $key }}' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-500/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                                <span>{{ $bm['label'] }}</span>
                                <span class="text-[10px] font-normal opacity-80">Mốc: {{ $bm['expected_words'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Word counter gauge -->
                <div class="flex items-center gap-5 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <div class="text-center">
                        <div class="text-xs uppercase font-bold text-slate-400">Vốn từ ghi nhận</div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-600 font-heading">
                            <span x-text="totalCount">0</span>
                            <span class="text-base text-slate-400 font-normal">/ 42 từ</span>
                        </div>
                    </div>

                    <!-- Evaluation Badge -->
                    <div class="p-3 rounded-2xl border"
                         :class="{
                             'bg-emerald-50 border-emerald-200 text-emerald-900': evaluationStatus.color === 'emerald',
                             'bg-teal-50 border-teal-200 text-teal-900': evaluationStatus.color === 'teal',
                             'bg-amber-50 border-amber-200 text-amber-900': evaluationStatus.color === 'amber',
                             'bg-rose-50 border-rose-200 text-rose-900': evaluationStatus.color === 'rose',
                         }">
                        <div class="text-xs font-black uppercase flex items-center gap-1.5" x-text="evaluationStatus.label"></div>
                        <p class="text-[11px] leading-tight mt-0.5 max-w-[200px]" x-text="evaluationStatus.message"></p>
                    </div>

                    <!-- Reset button -->
                    <button @click="clearAll()" title="Chọn lại" class="p-2.5 rounded-xl border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2 Main Tabs: Bảng Từ Vựng & Vòng Quay Hoạt Động -->
        <div class="flex items-center justify-center gap-3 mb-8">
            <button @click="activeTab = 'words'"
                    class="px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm transition flex items-center gap-2"
                    :class="activeTab === 'words' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span>📝</span> Danh sách từ vựng kiểm tra
            </button>
            <button @click="activeTab = 'wheel'"
                    class="px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm transition flex items-center gap-2"
                    :class="activeTab === 'wheel' ? 'bg-gradient-to-r from-rose-500 to-amber-500 text-white shadow-md shadow-rose-500/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span>🎡</span> Vòng quay trò chơi hôm nay
                <span class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-800 text-[10px] font-black">Hot</span>
            </button>
        </div>

        <!-- TAB 1: WORD CHECKLIST -->
        <div x-show="activeTab === 'words'" class="space-y-8">
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-950 text-xs flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">💡</span>
                    <span><strong>Hướng dẫn:</strong> Bấm vào những từ con bạn <strong>đã từng nói được ít nhất 1 lần</strong> (kể cả âm thanh ngọng hoặc từ tượng thanh như gâu gâu, meo meo).</span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="shareFacebook()" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                        <span>Chia sẻ Facebook</span>
                    </button>
                    <button @click="copyShareLink()" class="px-3 py-1.5 rounded-xl bg-white text-slate-700 border border-slate-300 hover:bg-slate-100 font-bold text-xs">
                        <span>Copy Link</span>
                    </button>
                </div>
            </div>

            @foreach($categories as $catKey => $cat)
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl">{{ $cat['icon'] }}</span>
                            <h3 class="font-bold text-base sm:text-lg text-slate-900 font-heading">{{ $cat['name'] }}</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">{{ count($cat['words']) }} từ</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        @foreach($cat['words'] as $item)
                            <div @click="toggleWord('{{ $item['word'] }}')"
                                 class="p-3.5 rounded-2xl border-2 transition-all duration-200 cursor-pointer flex flex-col justify-between select-none group"
                                 :class="isWordSelected('{{ $item['word'] }}') ? 'bg-emerald-50 border-emerald-500 shadow-md shadow-emerald-500/10' : 'bg-slate-50 hover:bg-white border-transparent hover:border-slate-200'">
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-2xl group-hover:scale-110 transition">{{ $item['icon'] }}</span>
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                                         :class="isWordSelected('{{ $item['word'] }}') ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 bg-white'">
                                        <svg x-show="isWordSelected('{{ $item['word'] }}')" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                        </svg>
                                    </div>
                                </div>

                                <div>
                                    <div class="font-bold text-xs sm:text-sm text-slate-800"
                                         :class="isWordSelected('{{ $item['word'] }}') ? 'text-emerald-900 font-black' : ''">
                                        {{ $item['word'] }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">Mốc chuẩn: {{ $item['level'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Action Next Steps -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 rounded-3xl p-8 sm:p-10 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left">
                    <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider">Bước tiếp theo</span>
                    <h2 class="text-2xl sm:text-3xl font-black font-heading">Muốn Giúp Con Tăng Vốn Từ Nhanh?</h2>
                    <p class="text-emerald-100 text-xs sm:text-sm max-w-xl">
                        Khám phá Lộ trình 30 ngày tương tác tại nhà và bộ thẻ kích âm thanh mẫu với 21 bài tập thực chiến.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <a href="{{ route('roadmap.index') }}" class="px-6 py-3.5 rounded-2xl bg-white text-emerald-800 hover:bg-emerald-50 font-black text-xs sm:text-sm text-center shadow-lg transition">
                        Bắt đầu Lộ trình 30 ngày &rarr;
                    </a>
                    <a href="{{ route('flashcards.index') }}" class="px-6 py-3.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-900 text-white border border-emerald-400/40 font-bold text-xs sm:text-sm text-center transition">
                        Luyện thẻ kích âm 🎴
                    </a>
                </div>
            </div>
        </div>

        <!-- TAB 2: LUCKY SPINNER -->
        <div x-show="activeTab === 'wheel'" class="max-w-3xl mx-auto space-y-8" x-cloak>
            <div class="bg-white rounded-3xl p-8 sm:p-10 text-center shadow-xl border border-slate-200">
                <div class="w-16 h-16 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                    🎡
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mb-2">
                    Vòng Quay Trò Chơi Tương Tác Hôm Nay
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mb-8 max-w-lg mx-auto">
                    Mẹ bỉm đang bí ý tưởng chơi gì cùng con tối nay? Bấm quay để hệ thống chọn ngẫu nhiên 1 hoạt động 5 phút kích hoạt ngôn ngữ!
                </p>

                <div class="my-8">
                    <template x-if="spunActivity">
                        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-tr from-amber-50 to-rose-50 border-2 border-rose-300 text-left space-y-4 animate-scaleIn">
                            <div class="flex items-center justify-between">
                                <span class="text-4xl" x-text="spunActivity.icon"></span>
                                <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-black uppercase" x-text="spunActivity.tag"></span>
                            </div>
                            <div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 font-heading" x-text="spunActivity.title"></h3>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                    <span>⏱️ Thời gian:</span>
                                    <strong class="text-slate-800" x-text="spunActivity.duration"></strong>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-rose-100 text-xs text-slate-700 leading-relaxed shadow-sm">
                                <strong class="text-rose-700 block mb-1">🎯 Mục tiêu kích âm:</strong>
                                <span x-text="spunActivity.goal"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="!spunActivity && !isSpinning">
                        <div class="py-12 px-6 rounded-3xl border-2 border-dashed border-slate-200 text-slate-400">
                            <span class="text-4xl block mb-2">🎲</span>
                            <p class="text-sm font-semibold">Chưa quay trò chơi nào. Bấm nút bên dưới để chọn trò chơi hôm nay!</p>
                        </div>
                    </template>

                    <template x-if="isSpinning">
                        <div class="py-12 px-6 rounded-3xl bg-rose-50 border border-rose-200 text-rose-600 animate-pulse">
                            <span class="text-4xl block mb-2 animate-spin">🎡</span>
                            <p class="text-sm font-bold">Đang quay tìm trò chơi thích hợp nhất cho mẹ và bé...</p>
                        </div>
                    </template>
                </div>

                <button @click="spinWheel()"
                        :disabled="isSpinning"
                        class="px-8 py-4 rounded-2xl bg-gradient-to-r from-rose-500 via-amber-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-black text-sm shadow-xl shadow-rose-500/30 transition transform hover:-translate-y-0.5 disabled:opacity-50">
                    <span x-text="isSpinning ? 'Đang quay...' : 'Quay Trò Chơi Mới Ngay 🎲'"></span>
                </button>
            </div>
        </div>

        <!-- Affiliate Product Recommendation Box (Tối ưu hóa kiếm tiền / Quảng cáo) -->
        <div class="mt-12 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-3xl shrink-0">
                    📚
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800">Gợi ý học cụ hỗ trợ</span>
                    <h3 class="font-bold text-base sm:text-lg text-slate-900 mt-1">Bộ Sách Ehon Kích Hoạt Âm Thanh & Thẻ Flashcard Khẩu Hình</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Được cộng đồng phụ huynh can thiệp ngôn ngữ tin dùng nhất.</p>
                </div>
            </div>
            <a href="https://shopee.vn" target="_blank" rel="nofollow noopener" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs sm:text-sm whitespace-nowrap shadow-md transition flex items-center gap-2 shrink-0">
                <span>Xem trên Shopee</span>
                <span>↗</span>
            </a>
        </div>

    </div>
</div>
@endsection
