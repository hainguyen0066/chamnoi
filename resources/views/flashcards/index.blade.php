@extends('layouts.app')

@section('title', 'Thẻ Kích Âm & Bài Tập Cơ Miệng Cho Trẻ - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16" x-data="{
    activeCategory: 'dong_vat',
    currentlyPlaying: null,
    audioPlayer: null,
    init() {
        this.audioPlayer = new Audio();
    },
    playAudio(audioUrl, text) {
        this.currentlyPlaying = text;
        
        if (audioUrl) {
            try {
                this.audioPlayer.pause();
                this.audioPlayer.currentTime = 0;
                this.audioPlayer.src = audioUrl;
                this.audioPlayer.onended = () => {
                    this.currentlyPlaying = null;
                };
                this.audioPlayer.onerror = () => {
                    this.speakFallback(text);
                };
                const playPromise = this.audioPlayer.play();
                if (playPromise !== undefined) {
                    playPromise.catch(() => {
                        this.speakFallback(text);
                    });
                }
            } catch (err) {
                this.speakFallback(text);
            }
        } else {
            this.speakFallback(text);
        }
    },
    speakFallback(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'vi-VN';
            utterance.rate = 0.85;
            utterance.pitch = 1.1;
            utterance.onend = () => { this.currentlyPlaying = null; };
            utterance.onerror = () => { this.currentlyPlaying = null; };
            window.speechSynthesis.speak(utterance);
        } else {
            this.currentlyPlaying = null;
        }
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Thẻ kích âm & Luyện cơ miệng</span>
        </nav>

        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Học cụ tương tác trực tuyến
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Thẻ Kích Âm & Trò Chơi Phát Âm
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Bấm vào từng thẻ để <strong>nghe giọng phát âm mẫu chậm rãi</strong>. Cùng con lặp lại âm thanh và làm theo các gợi ý khẩu hình của chuyên gia.
            </p>
        </div>

        <!-- Category Switcher Tabs -->
        <div class="flex items-center justify-center gap-2 overflow-x-auto pb-4 mb-8">
            <button @click="activeCategory = 'dong_vat'"
                    class="px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm transition shrink-0 flex items-center gap-2"
                    :class="activeCategory === 'dong_vat' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'">
                <span>🐶</span> Tiếng kêu con vật
            </button>

            <button @click="activeCategory = 'phuong_tien'"
                    class="px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm transition shrink-0 flex items-center gap-2"
                    :class="activeCategory === 'phuong_tien' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'">
                <span>🚗</span> Âm thanh xe cộ
            </button>

            <button @click="activeCategory = 'nhu_cau'"
                    class="px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm transition shrink-0 flex items-center gap-2"
                    :class="activeCategory === 'nhu_cau' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'">
                <span>🥣</span> Từ ngữ nhu cầu cốt lõi
            </button>

            <button @click="activeCategory = 'co_mieng'"
                    class="px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm transition shrink-0 flex items-center gap-2"
                    :class="activeCategory === 'co_mieng' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'">
                <span>💨</span> Bài tập cơ miệng
            </button>
        </div>

        <!-- Cards Panels -->
        @foreach($flashcards as $catKey => $catData)
            <div x-show="activeCategory === '{{ $catKey }}'" x-cloak class="space-y-6">
                <!-- Category intro -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 font-heading">{{ $catData['category_name'] }}</h2>
                        <p class="text-xs text-slate-500">{{ $catData['description'] }}</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100 shrink-0">
                        {{ count($catData['items']) }} thẻ luyện tập
                    </span>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($catData['items'] as $item)
                        <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border flex flex-col justify-between group cursor-pointer"
                             :class="currentlyPlaying === '{{ $item['word'] }}' ? 'border-emerald-500 ring-4 ring-emerald-100 shadow-xl' : 'border-slate-200/80'"
                             @click="playAudio('{{ $item['audio'] ?? '' }}', '{{ $item['word'] }}')">
                            <div>
                                <div class="w-20 h-20 rounded-3xl bg-slate-50 text-slate-800 flex items-center justify-center text-4xl mx-auto mb-4 border border-slate-100 group-hover:scale-110 transition shadow-inner"
                                     :class="currentlyPlaying === '{{ $item['word'] }}' ? 'scale-110 bg-emerald-50 animate-bounce' : ''">
                                    {{ $item['icon'] }}
                                </div>

                                <div class="text-center mb-4">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $item['label'] }}</span>
                                    <div class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 text-emerald-700 flex items-center justify-center gap-2">
                                        <span>"{{ $item['word'] }}"</span>
                                        <span x-show="currentlyPlaying === '{{ $item['word'] }}'" class="text-base animate-pulse">🔊</span>
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-100 text-xs text-amber-950 leading-relaxed mb-4">
                                    <strong class="font-bold block mb-0.5">Cách mẹ tương tác:</strong>
                                    {{ $item['tip'] }}
                                </div>
                            </div>

                            <button type="button" 
                                    class="w-full py-3 rounded-2xl font-bold text-xs shadow-md transition flex items-center justify-center gap-2"
                                    :class="currentlyPlaying === '{{ $item['word'] }}' ? 'bg-gradient-to-r from-amber-500 to-rose-500 text-white ring-2 ring-amber-300 animate-pulse' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white'">
                                <template x-if="currentlyPlaying === '{{ $item['word'] }}'">
                                    <span class="flex items-center gap-2">
                                        <span class="animate-spin text-sm">💿</span>
                                        <span>Đang phát âm thanh...</span>
                                    </span>
                                </template>
                                <template x-if="currentlyPlaying !== '{{ $item['word'] }}'">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z"/>
                                        </svg>
                                        <span>Bấm nghe giọng mẫu</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection
