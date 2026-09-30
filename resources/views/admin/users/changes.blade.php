<x-admin-layout>
    <x-slot name="header">Lịch sử thay đổi: {{ $user->username ?? $user->name }}</x-slot>

    <div class="mb-4 flex items-center gap-3 text-sm">
        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:underline">&larr; Quay lại chi tiết tài khoản</a>
        <span class="text-gray-400">|</span>
        <span class="text-gray-600">{{ $user->name }} · {{ $user->email ?? '—' }} · {{ $user->phone ?? '—' }}</span>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3">Ngày giờ</th>
                    <th class="px-5 py-3">Thay đổi</th>
                    <th class="px-5 py-3">Chi tiết</th>
                    <th class="px-5 py-3">Người thực hiện</th>
                    <th class="px-5 py-3">IP</th>
                    <th class="px-5 py-3">Trình duyệt</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($logs as $log)
                    <tr class="align-top">
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y/m/d H:i:s') }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $log->action }}</td>
                        <td class="px-5 py-3 text-gray-600">
                            @php $details = $log->details(); @endphp
                            @if ($details === [])
                                <span class="text-gray-400">—</span>
                            @else
                                <ul class="space-y-0.5">
                                    @foreach ($details as $d)
                                        <li>
                                            <span class="text-gray-500">{{ $d['label'] }}:</span>
                                            @if ($d['label'] === 'Mật khẩu')
                                                <span>đã đổi</span>
                                            @else
                                                <span class="line-through text-gray-400 break-all">{{ $d['old'] }}</span>
                                                <span class="text-gray-400">→</span>
                                                <span class="text-gray-800 break-all">{{ $d['new'] }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            @php $badge = ['user' => 'bg-blue-100 text-blue-700', 'admin' => 'bg-purple-100 text-purple-700'][$log->actor_type] ?? 'bg-gray-100 text-gray-600'; @endphp
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">{{ $log->actorLabel() }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap">{{ $log->ip ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap" title="{{ $log->user_agent }}">{{ $log->browserLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-6 text-center text-gray-400">Chưa có thay đổi nào được ghi nhận.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</x-admin-layout>
