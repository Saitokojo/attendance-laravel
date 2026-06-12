<x-admin-layout title="Employees">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">{{ $users->count() }} employee(s)</p>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            Add Employee
        </a>
    </div>

    @if ($users->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500 text-sm">
            No employees yet.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Email</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Office</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <tr class="{{ $user->is_active ? 'hover:bg-gray-50' : 'bg-gray-50 opacity-60' }}">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->office?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right space-x-3">
                                <a href="{{ route('admin.users.summary', $user) }}"
                                   class="text-gray-500 hover:text-gray-700 font-medium">Summary</a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>

                                @if ($user->is_active)
                                    <form method="POST"
                                          action="{{ route('admin.users.deactivate', $user) }}"
                                          class="inline"
                                          onsubmit="return confirm('Deactivate {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 font-medium">Deactivate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin-layout>
