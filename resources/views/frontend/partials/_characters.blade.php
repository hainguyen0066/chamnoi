{{--
    Khối "Giới thiệu nhân vật" trang chủ.
    Muốn thêm/sửa nhân vật: chỉnh mảng $characterSlides bên dưới — mỗi phần tử
    là 1 slide gồm 2 môn phái (không cần đụng vào HTML).
--}}
@php
    $fe = fn (string $path) => asset('frontend/assets/' . $path);

    // Mỗi slide: element (kim/moc/thuy/hoa/tho) + 2 nhân vật [class icon, name img, figure img, thông tin]
    $characterSlides = [
        [
            'element' => 'kim', 'name_class' => 'namenv-kim',
            'chars' => [
                ['class' => 'class-thienvuong', 'name' => 'name-thienvuong', 'figure' => 'nhanvat-thienvuong',
                 'gender' => 'Nam', 'vohoc' => 'Đao/Thương/Chùy Pháp',
                 'dacdiem' => 'Cận chiến, sát thương cao, khả năng phòng ngự tốt, chiến đấu tuyến đầu.'],
                ['class' => 'class-thieulam', 'name' => 'name-thieulam', 'figure' => 'nhanvat-thieulam',
                 'gender' => 'Nam', 'vohoc' => 'Côn/Đao/Quyền Pháp',
                 'dacdiem' => 'Thủ trâu, khống chế tốt, chống đỡ tuyến đầu.'],
            ],
        ],
        [
            'element' => 'moc', 'name_class' => 'namenv',
            'chars' => [
                ['class' => 'class-ngudoc', 'name' => 'name-ngudoc', 'figure' => 'nhanvat-ngudoc',
                 'gender' => 'Nữ', 'vohoc' => 'Đao/Chưởng Pháp/Bùa Chú',
                 'dacdiem' => 'Dùng độc, sát thương theo thời gian, gây suy giảm khả năng phòng ngự.'],
                ['class' => 'class-duongmon', 'name' => 'name-duongmon', 'figure' => 'nhanvat-duongmon',
                 'gender' => 'Nam', 'vohoc' => 'Phi Đao/Phi Tiêu/Tụ Tiễn/Hãm Tĩnh',
                 'dacdiem' => 'Công kích tầm xa, đặt bẫy, đánh du kích.'],
            ],
        ],
        [
            'element' => 'thuy', 'name_class' => 'namenv',
            'chars' => [
                ['class' => 'class-thuyyen', 'name' => 'name-thuyyen', 'figure' => 'nhanvat-thuyyen',
                 'gender' => 'Nữ', 'vohoc' => 'Đao Pháp/Song Đao',
                 'dacdiem' => 'Kỹ năng công kích tầm xa, sát thương cao, khống chế tốt.'],
                ['class' => 'class-ngamy', 'name' => 'name-ngamy', 'figure' => 'nhanvat-ngamy',
                 'gender' => 'Nữ', 'vohoc' => 'Kiếm/Chưởng Pháp/Hỗ Trợ',
                 'dacdiem' => 'Kỹ năng công kích tầm xa, khống chế, hỗ trợ phục hồi.'],
            ],
        ],
        [
            'element' => 'hoa', 'name_class' => 'namenv',
            'chars' => [
                ['class' => 'class-thiennhan', 'name' => 'name-thiennhan', 'figure' => 'nhanvat-thiennhan',
                 'gender' => 'Nữ', 'vohoc' => 'Đao/Mâu Pháp/Bùa Chú',
                 'dacdiem' => 'Lối chơi linh hoạt, cận chiến đơn mục tiêu, tầm xa diện rộng, bùa suy yếu địch.'],
                ['class' => 'class-caibang', 'name' => 'name-caibang', 'figure' => 'nhanvat-caibang',
                 'gender' => 'Nam', 'vohoc' => 'Bổng/Chưởng Pháp',
                 'dacdiem' => 'Công kích tầm xa, sát thương diện rộng.'],
            ],
        ],
        [
            'element' => 'tho', 'name_class' => 'namenv',
            'chars' => [
                ['class' => 'class-conlon', 'name' => 'name-conlon', 'figure' => 'nhanvat-conlon',
                 'gender' => 'Nữ', 'vohoc' => 'Kiếm/Đao Pháp/Bùa Chú',
                 'dacdiem' => 'Công kích tầm xa, khống chế tốt, bùa chú suy giảm chiến lực kẻ địch.'],
                ['class' => 'class-vodang', 'name' => 'name-vodang', 'figure' => 'nhanvat-vodang',
                 'gender' => 'Nam', 'vohoc' => 'Kiếm/Quyền Pháp',
                 'dacdiem' => 'Khống chế tốt, công thủ cân bằng.'],
            ],
        ],
    ];
@endphp

<div class="nhanvat">
    <div>
        <div class="title"><img src="{{ $fe('images/title-gtnhanvat.png') }}"></div>

        <div>
            <div class="nv-slider">
                @foreach ($characterSlides as $slide)
                    <div class="rev-nvslide">
                        <div>
                            <div class="block-skill active">
                                <div class="skill nv-skill1 skill-tab">
                                    @foreach ($slide['chars'] as $index => $char)
                                        <div class="nvskill-img {{ $index === 0 ? 'active-skill' : '' }}">
                                            <img src="{{ $fe('images/' . $char['class'] . '.png') }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="nhanvat-tab">
                                    @foreach ($slide['chars'] as $index => $char)
                                        <div class="tab {{ $index === 0 ? 'active' : '' }}">
                                            <div class="nv-layout">
                                                <div class="nv-info">
                                                    <div class="nv-info--bg">
                                                        <div class="{{ $slide['name_class'] }}">
                                                            <img src="{{ $fe('images/' . $char['name'] . '.png') }}">
                                                        </div>
                                                        <div class="infonutube">
                                                            <div class="contentnv">
                                                                <p><b>Giới tính: </b><span>{{ $char['gender'] }}</span></p>
                                                                <p><b>Võ học: </b><span>{{ $char['vohoc'] }}</span></p>
                                                                <p><b>Đặc điểm: </b><span>{{ $char['dacdiem'] }}</span></p>
                                                            </div>
                                                            <div class="utubenv">
                                                                <div class="utube-bg">
                                                                    <img src="{{ $fe('images/utube-img.png') }}" alt="">
                                                                    <img class="play btnvid-tieudaotruongkiem" src="{{ $fe('images/play.png') }}" alt="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="labelclass">
                                                            <img src="{{ $fe('images/label-' . $slide['element'] . '.png') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nv-figure">
                                                    <img class="" src="{{ $fe('images/' . $char['figure'] . '.png') }}" />
                                                    <img class="nveffect" src="{{ $fe('images/effect-' . $slide['element'] . '.png') }}" />
                                                    <img class="nvline" src="{{ $fe('images/nhanvat-thanhdoc.png') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
