<x-admin-layout title="Attendance">
    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Office</label>
            <select name="office_id"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                <option value="">All offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" @selected($officeId == $office->id)>
                        {{ $office->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            Filter
        </button>
        @if($officeId || $date !== \Carbon\Carbon::today()->toDateString())
            <a href="{{ route('admin.attendance.index') }}"
               class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </form>

    @if($records->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500 text-sm">
            No attendance records for {{ $date }}{{ $officeId ? ' in this office' : '' }}.
        </div>
    @else
        <p class="text-sm text-gray-500 mb-2">{{ $records->count() }} record(s)</p>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Employee</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Office</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Check-in</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Check-out</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $record->user->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $record->office->name }}</td>
                            <td class="px-5 py-3 font-mono text-gray-700">
                                {{ $record->check_in_at->setTimezone($record->office->timezone)->format('H:i') }}
                            </td>
                            <td class="px-5 py-3 font-mono text-gray-700">
                                {{ $record->check_out_at?->setTimezone($record->office->timezone)->format('H:i') ?? '—' }}
                            </td>
                            <td class="px-5 py-3">
                                @if($record->status === 'on_time')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">On time</span>
                                @elseif($record->status === 'late')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">Late</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin-layout>
