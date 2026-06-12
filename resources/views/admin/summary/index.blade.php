<x-admin-layout title="Attendance Summary — {{ $user->name }}">
    <div class="max-w-4xl">
        <a href="{{ route('admin.users.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 mb-6 inline-block">&larr; Back to employees</a>

        {{-- Header + month picker --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->office?->name ?? 'No office' }}</p>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected($m === $month)>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
            @foreach([
                ['label' => 'Calendar Days', 'value' => $summary['total_days'],  'color' => 'bg-gray-50  text-gray-700'],
                ['label' => 'Present',        'value' => $summary['present'],     'color' => 'bg-green-50 text-green-700'],
                ['label' => 'On Time',        'value' => $summary['on_time'],     'color' => 'bg-blue-50  text-blue-700'],
                ['label' => 'Late',           'value' => $summary['late'],        'color' => 'bg-red-50   text-red-700'],
                ['label' => 'On Leave',       'value' => $summary['on_leave'],    'color' => 'bg-yellow-50 text-yellow-700'],
                ['label' => 'Absent',         'value' => $summary['absent'],      'color' => 'bg-orange-50 text-orange-700'],
            ] as $card)
                <div class="rounded-lg p-4 {{ $card['color'] }} shadow-sm text-center">
                    <p class="text-2xl font-bold">{{ $card['value'] }}</p>
                    <p class="text-xs mt-1 font-medium">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Daily records --}}
        @if($records->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-400 text-sm">
                No attendance records for {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}.
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Date</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Check-in</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Check-out</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Duration</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($records as $rec)
                            @php
                                $tz = $rec->office?->timezone ?? 'UTC';
                                $duration = $rec->check_out_at
                                    ? gmdate('H:i', $rec->check_in_at->diffInSeconds($rec->check_out_at))
                                    : null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $rec->date->format('d M Y') }}
                                    <span class="text-gray-400 font-normal">({{ $rec->date->format('D') }})</span>
                                </td>
                                <td class="px-5 py-3 font-mono text-gray-700">
                                    {{ $rec->check_in_at->setTimezone($tz)->format('H:i') }}
                                </td>
                                <td class="px-5 py-3 font-mono text-gray-700">
                                    {{ $rec->check_out_at?->setTimezone($tz)->format('H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $duration ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @if($rec->status === 'on_time')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">On time</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">Late</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin-layout>
