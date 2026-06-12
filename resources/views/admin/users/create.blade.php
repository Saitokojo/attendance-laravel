<x-admin-layout title="Add Employee">
    <div class="max-w-2xl">
        <a href="{{ route('admin.users.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 mb-6 inline-block">&larr; Back to employees</a>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                @include('admin.users._form', ['user' => null])

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
