<x-admin-layout title="Dashboard">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <a href="{{ route('admin.offices.index') }}"
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500">Offices</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">
                {{ \App\Models\Office::count() }}
            </p>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-500">Employees</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">
                {{ \App\Models\User::where('role', 'employee')->count() }}
            </p>
        </a>
    </div>

    <h2 class="text-lg font-semibold text-gray-900 mb-4">Today's Attendance</h2>
    @if($todayStats->isEmpty())
        <p class="text-sm text-gray-500">No active offices configured.</p>
    @else
        <div class="overflow-hidden shadow-sm rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Office</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checked In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checked Out</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($todayStats as $stat)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $stat['office']->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $stat['checked_in'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $stat['checked_out'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin-layout>
