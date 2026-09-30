@props(['status'])

@php
    // Badge trạng thái giao dịch nạp, dùng chung ở dashboard/deposits/users.
    [$classes, $label] = match ($status) {
        'completed' => ['bg-green-100 text-green-700', 'Hoàn tất'],
        'pending' => ['bg-yellow-100 text-yellow-700', 'Chờ xử lý'],
        'failed' => ['bg-red-100 text-red-700', 'Thất bại'],
        default => ['bg-gray-100 text-gray-600', $status],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-block px-2 py-0.5 rounded text-xs font-medium {$classes}"]) }}>
    {{ $label }}
</span>
