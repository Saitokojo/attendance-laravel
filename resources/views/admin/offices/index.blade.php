<x-admin-layout title="Offices">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">{{ $offices->count() }} office(s)</p>
        <a href="{{ route('admin.offices.create') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            Add Office
        </a>
    </div>

    @if ($offices->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500 text-sm">
            No offices yet.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Coordinates</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Radius</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Late After</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Timezone</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($offices as $office)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $office->name }}</td>
                            <td class="px-5 py-3 text-gray-600 font-mono text-xs">
                                {{ $office->latitude }}, {{ $office->longitude }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $office->radius_meters }} m</td>
                            <td class="px-5 py-3 text-gray-600">{{ $office->late_after }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $office->timezone }}</td>
                            <td class="px-5 py-3">
                                @if ($office->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.offices.edit', $office) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin-layout>
