<x-admin-layout title="QR Sessions">
    <div class="max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">QR Sessions</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Office</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($offices as $office)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $office->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($office->activeSession)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($office->activeSession)
                                    {{ $office->activeSession->expires_at->format('H:i:s') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.qr.store') }}">
                                    @csrf
                                    <input type="hidden" name="office_id" value="{{ $office->id }}">
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md
                                                   {{ $office->activeSession ? 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}
                                                   transition-colors">
                                        {{ $office->activeSession ? 'Refresh Session' : 'Start Session' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">
                                No active offices found.
                                <a href="{{ route('admin.offices.create') }}" class="text-indigo-600 hover:underline ml-1">Add an office</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
