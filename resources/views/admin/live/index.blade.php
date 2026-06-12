<x-admin-layout title="Live Status">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Auto-refreshes every 30 seconds. Last updated: <span id="last-updated">—</span></p>
        <button onclick="location.reload()"
                class="px-3 py-1.5 text-xs font-medium bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition-colors">
            Refresh Now
        </button>
    </div>

    @forelse($rows as $row)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-3">
                <h2 class="text-base font-semibold text-gray-800">{{ $row['office']->name }}</h2>
                <span class="text-xs text-gray-400">{{ $row['today'] }}</span>
                @php
                    $checkedIn  = collect($row['employees'])->where('badge', 'checked_in')->count();
                    $checkedOut = collect($row['employees'])->where('badge', 'checked_out')->count();
                    $onLeave    = collect($row['employees'])->where('badge', 'on_leave')->count();
                    $absent     = collect($row['employees'])->where('badge', 'absent')->count();
                @endphp
                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">{{ $checkedIn }} in</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ $checkedOut }} out</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700">{{ $onLeave }} leave</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ $absent }} absent</span>
            </div>

            @if(collect($row['employees'])->isEmpty())
                <p class="text-sm text-gray-400 italic">No active employees in this office.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($row['employees'] as $emp)
                        @php $record = $emp['record']; @endphp
                        <div class="bg-white rounded-lg shadow-sm px-4 py-3 flex items-center gap-3
                            {{ $emp['badge'] === 'checked_in'  ? 'border-l-4 border-green-400' : '' }}
                            {{ $emp['badge'] === 'checked_out' ? 'border-l-4 border-blue-400'  : '' }}
                            {{ $emp['badge'] === 'on_leave'    ? 'border-l-4 border-yellow-400': '' }}
                            {{ $emp['badge'] === 'absent'      ? 'border-l-4 border-gray-200'  : '' }}">

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $emp['user']->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if($record)
                                        In: {{ $record->check_in_at->setTimezone($row['office']->timezone)->format('H:i') }}
                                        @if($record->check_out_at)
                                            · Out: {{ $record->check_out_at->setTimezone($row['office']->timezone)->format('H:i') }}
                                        @endif
                                    @elseif($emp['badge'] === 'on_leave')
                                        On approved leave
                                    @else
                                        Not yet checked in
                                    @endif
                                </p>
                            </div>

                            @if($emp['badge'] === 'checked_in')
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">In</span>
                            @elseif($emp['badge'] === 'checked_out')
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Out</span>
                            @elseif($emp['badge'] === 'on_leave')
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Leave</span>
                            @else
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-400">Absent</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500">No active offices configured.</p>
    @endforelse
</x-admin-layout>

@push('scripts')
<script>
    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
    setTimeout(() => location.reload(), 30000);
</script>
@endpush
