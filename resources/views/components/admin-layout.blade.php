@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside class="w-56 bg-gray-900 text-white flex flex-col shrink-0">
            <div class="h-16 flex items-center px-5 border-b border-gray-700">
                <span class="text-base font-semibold tracking-wide">Attendance</span>
            </div>

            <nav class="flex-1 py-4 space-y-0.5 px-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.offices.index') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.offices.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Offices
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Employees
                </a>
                <a href="{{ route('admin.qr.index') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.qr.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    QR Sessions
                </a>
                <a href="{{ route('admin.attendance.index') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.attendance.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Attendance
                </a>
                <a href="{{ route('admin.live.index') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.live.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Live Status
                </a>
                <a href="{{ route('admin.leaves.index') }}"
                   class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.leaves.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Leave Requests
                </a>
            </nav>

            <div class="p-3 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 text-sm text-gray-400 hover:text-white rounded-md hover:bg-gray-700 transition-colors truncate">
                        Sign out ({{ auth()->user()->name }})
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="h-16 bg-white shadow-sm flex items-center px-8 shrink-0">
                <h1 class="text-lg font-semibold text-gray-800">{{ $title }}</h1>
            </header>

            <main class="flex-1 p-8">
                @if (session('success'))
                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
