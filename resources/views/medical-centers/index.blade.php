@extends('layouts.app')

@section('title', 'Danh Bạ Bệnh Viện & Cơ Sở Can Thiệp Chậm Nói Uy Tín - Mầm Ngôn Ngữ')

@section('content')
<div class="bg-slate-50 py-10 lg:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 items-center">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Danh bạ bệnh viện & Cơ sở can thiệp uy tín</span>
        </nav>

        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                Địa chỉ y tế chính thống
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-heading mt-3 mb-4">
                Danh Bạ Bệnh Viện & Can Thiệp Sớm
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Tổng hợp các bệnh viện Nhi khoa tuyến đầu, Viện Sức khỏe Tâm thần và khoa Âm ngữ trị liệu có quy trình chẩn đoán bài bản tại Việt Nam.
            </p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 mb-10">
            <form method="GET" action="{{ route('medical-centers.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm bệnh viện, khoa, địa chỉ..." class="w-full pl-11 pr-4 py-2.5 text-xs sm:text-sm rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <a href="{{ route('medical-centers.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition shrink-0 {{ empty($selectedCity) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Toàn quốc
                    </a>
                    @foreach($cities as $city)
                        <a href="{{ route('medical-centers.index', ['city' => $city]) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition shrink-0 {{ $selectedCity === $city ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $city }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Centers List -->
        <div class="space-y-6">
            @forelse($centers as $center)
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-md transition border border-slate-200 flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="flex-1 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                {{ $center->city }}
                            </span>
                            @if($center->is_verified)
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 flex items-center gap-1">
                                    <span>✓</span> Đơn vị chính thống
                                </span>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 font-heading">
                            {{ $center->name }}
                        </h3>

                        <div class="text-xs sm:text-sm text-slate-600 space-y-1">
                            <div class="flex items-start gap-2">
                                <span class="text-slate-400">📍</span>
                                <span>{{ $center->address }}</span>
                            </div>
                            @if($center->phone)
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-400">📞</span>
                                    <a href="tel:{{ $center->phone }}" class="text-emerald-700 font-bold hover:underline font-mono">{{ $center->phone }}</a>
                                </div>
                            @endif
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 leading-relaxed">
                            <strong class="text-slate-800 block mb-0.5">Chuyên khoa & Dịch vụ:</strong>
                            {{ $center->specialty }}
                        </div>

                        @if($center->booking_tip)
                            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 text-xs text-amber-900 leading-relaxed">
                                <strong class="font-bold">💡 Hướng dẫn đặt khám:</strong>
                                {{ $center->booking_tip }}
                            </div>
                        @endif
                    </div>

                    @if($center->website)
                        <div class="md:shrink-0 self-start">
                            <a href="{{ $center->website }}" target="_blank" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:border-emerald-500 hover:text-emerald-600 font-bold text-xs text-slate-700 transition inline-flex items-center gap-1.5">
                                <span>Trang web bệnh viện</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 text-slate-400">
                    Không tìm thấy cơ sở y tế nào phù hợp với bộ lọc hiện tại.
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
