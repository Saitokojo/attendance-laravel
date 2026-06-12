<x-admin-layout title="Leave Requests">

    {{-- Filter tabs --}}
    <div class="flex gap-2 mb-6">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $val => $label)
            <a href="{{ route('admin.leaves.index', ['status' => $val]) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ $status === $val ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($leaves->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-400 text-sm">
            No {{ $status === 'all' ? '' : $status }} leave requests.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Employee</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Dates</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Days</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Reason</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leaves as $leave)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $leave->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $leave->user->office?->name ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3 capitalize text-gray-700">{{ $leave->type }}</td>
                            <td class="px-5 py-3 text-gray-700 whitespace-nowrap">
                                {{ $leave->start_date->format('d M Y') }}
                                @if(!$leave->start_date->equalTo($leave->end_date))
                                    → {{ $leave->end_date->format('d M Y') }}
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-700">{{ $leave->days }}</td>
                            <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $leave->reason ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if($leave->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($leave->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Approved</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">Rejected</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($leave->status === 'pending')
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('admin.leaves.approve', $leave) }}">
                                            @csrf @method('PATCH')
                                            <button class="px-3 py-1 text-xs font-medium bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.leaves.reject', $leave) }}">
                                            @csrf @method('PATCH')
                                            <button class="px-3 py-1 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">
                                        by {{ $leave->reviewer?->name ?? '—' }}
                                        @if($leave->reviewed_at)
                                            · {{ $leave->reviewed_at->diffForHumans() }}
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $leaves->links() }}</div>
    @endif
</x-admin-layout>
