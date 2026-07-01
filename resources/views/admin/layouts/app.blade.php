<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
<div
    x-data="{
        sidebarOpen: false,
        profileOpen: false
    }"
    class="min-h-screen"
>
    <div
        x-cloak
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
        @click="sidebarOpen = false"
    ></div>

    <aside
    class="fixed top-0 left-0 z-50 h-[100dvh] w-64 bg-white border-r border-gray-200 transition-transform duration-300 lg:translate-x-0 flex flex-col"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
        <div class="h-16 px-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm3.707 8.707l-4 4A1 1 0 018 12V8a1 1 0 011.707-.707L12.414 10l-2.707 2.707V9.414l4 4a1 1 0 01-1.414 1.414z"/>
                    </svg>
                </div>

                <div>
                    <div class="text-xl font-bold tracking-tight">PMS</div>
                    <div class="text-xs text-gray-500 -mt-0.5">Device System</div>
                </div>
            </div>

            <button
                type="button"
                class="lg:hidden p-2 rounded-lg hover:bg-gray-100"
                @click="sidebarOpen = false"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-3 py-4 overflow-y-auto h-[calc(100vh-4rem)] flex flex-col">
            <nav class="space-y-1 flex-1">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18M12 3v18"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a
                    href="{{ route('admin.colleges.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.colleges.*') || request()->routeIs('admin.offices.*') || request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.colleges.*') || request()->routeIs('admin.offices.*') || request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span>Colleges</span>
                </a>

                <a
                    href="{{ route('admin.devices.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.devices.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.devices.*') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 4L4 7m8 4l8-4"/>
                    </svg>
                    <span>Equipment Manager</span>
                </a>


                <a
                    href="{{ route('admin.reports.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6m4 6V7m4 10v-3M5 19h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Reports</span>
                </a>

                <a
                    href="{{ route('admin.scanner') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.scanner') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.scanner') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7V5a1 1 0 011-1h2m10 0h2a1 1 0 011 1v2m0 10v2a1 1 0 01-1 1h-2M7 20H5a1 1 0 01-1-1v-2m3-5h10"/>
                    </svg>
                    <span>QR Scanner</span>
                </a>

                @if(auth()->user() && auth()->user()->isAdmin())
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-4a4 4 0 100-8 4 4 0 000 8zm6 4a4 4 0 10-8 0v2h8v-2z"/>
                        </svg>
                        <span>Users</span>
                    </a>

                    <a
                        href="{{ route('admin.logs.index') }}"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('admin.logs.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.logs.*') ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h6m-6 4h6"/>
                        </svg>
                        <span>Activity Logs</span>
                    </a>
                @endif
            </nav>

            <div class="mt-6 border-t border-gray-200 pt-4 space-y-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H9m4 8H7a2 2 0 01-2-2V6a2 2 0 012-2h6"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>

                <div class="rounded-xl bg-gray-50 border border-gray-200 px-3 py-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-900 truncate">
                                {{ auth()->user()->name ?? 'User' }}
                            </div>

                            <div class="text-xs text-gray-500 truncate">
                                {{ auth()->user()->email ?? '' }}
                            </div>
                        </div>

                        @if(auth()->user())
                            <span class="shrink-0 inline-flex rounded-full {{ auth()->user()->isAdmin() ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }} px-2 py-0.5 text-[11px] font-medium">
                                {{ auth()->user()->roleLabel() }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div class="lg:ml-64 min-h-screen">
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200">
            <div class="h-16 px-4 sm:px-6 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center rounded-xl p-2 text-gray-600 hover:bg-gray-100"
                        @click="sidebarOpen = true"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="truncate text-lg sm:text-xl font-semibold text-gray-900">
                        @yield('page_title', 'Admin')
                        @if(trim($__env->yieldContent('breadcrumbs')))
                            <nav class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                              @yield('breadcrumbs')
                            </nav>
                    @endif
                    </h1>
                </div>

                <div class="relative shrink-0">
                    <button
                        type="button"
                        class="flex items-center gap-3 rounded-xl p-1.5 hover:bg-gray-100"
                        @click="profileOpen = !profileOpen"
                    >
                        <div class="hidden sm:block text-right">
                            <div class="text-sm font-medium text-gray-900">
                                {{ auth()->user()->name ?? 'Admin' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ auth()->user()->email ?? 'admin@example.com' }}
                            </div>
                        </div>

                        <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center overflow-hidden ring-2 ring-white shadow-sm">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </button>

                    <div
                        x-cloak
                        x-show="profileOpen"
                        x-transition
                        @click.away="profileOpen = false"
                        class="absolute right-0 mt-2 w-72 rounded-2xl bg-white shadow-xl ring-1 ring-gray-200 overflow-hidden"
                    >
                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="font-semibold text-gray-900">
                                {{ auth()->user()->name ?? 'Admin' }}
                            </div>
                            <div class="text-sm text-gray-500 truncate">
                                {{ auth()->user()->email ?? 'admin@example.com' }}
                            </div>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                            <a href="{{ route('admin.devices.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Device Manager</a>
                            <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Reports</a>
                            <a href="{{ route('admin.scanner') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">QR Scanner</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6">
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
@livewireScripts
</body>
</html>
