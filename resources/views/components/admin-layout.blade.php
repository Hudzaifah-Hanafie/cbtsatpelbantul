<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Container Utama: h-screen & overflow-hidden mengunci layar agar tidak scroll body -->
        <div class="h-screen flex overflow-hidden bg-gray-100" x-data="{ sidebarOpen: false }">
            
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col border-r border-gray-200"
                   :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                
                <!-- Logo Sidebar -->
                <div class="flex items-center justify-center h-16 bg-white border-b shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600" />
                        <span class="text-xl font-bold text-gray-800 tracking-tight">Admin Panel</span>
                    </a>
                </div>

                <!-- Navigasi (Scrollable secara mandiri jika menu banyak) -->
                <div class="flex-1 overflow-y-auto py-4">
                    <nav class="space-y-1 px-3">
                        <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="home">
                            {{ __('Dashboard') }}
                        </x-admin-nav-link>

                        <div class="pt-6 pb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                            Manajemen Ujian
                        </div>
                        <x-admin-nav-link :href="route('admin.tests.index')" :active="request()->routeIs('admin.tests.*') || request()->routeIs('admin.questions.*')" icon="clipboard-list">
                            {{ __('Daftar Ujian & Soal') }}
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.results.index')" :active="request()->routeIs('admin.results.*')" icon="chart-bar">
                            {{ __('Hasil & Nilai') }}
                        </x-admin-nav-link>

                        <div class="pt-6 pb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                            Pengaturan
                        </div>
                        <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="users">
                            {{ __('Manajemen User') }}
                        </x-admin-nav-link>
                    </nav>
                </div>

                <!-- Bagian Bawah: Profile & Logout -->
                <div class="border-t p-4 shrink-0 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="Logout">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Backdrop untuk Mobile -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" style="display: none;">
            </div>

            <!-- Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <!-- Header Atas (Sticky) -->
                <header class="bg-white shadow-sm shrink-0 z-10 border-b border-gray-200">
                    <!-- Versi Mobile -->
                    <div class="flex items-center justify-between px-4 py-3 lg:hidden">
                        <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 hover:text-indigo-600 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <span class="font-bold text-gray-800 text-lg">
                            @if (isset($header)) {{ $header }} @endif
                        </span>
                        <div class="w-6"></div>
                    </div>

                    <!-- Versi Desktop -->
                    @if (isset($header))
                        <div class="hidden lg:block px-8 py-6">
                            <div class="max-w-7xl mx-auto">
                                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                                    {{ $header }}
                                </h1>
                            </div>
                        </div>
                    @endif
                </header>

                <!-- Area Konten Utama (Hanya bagian ini yang bisa di-scroll) -->
                <main class="flex-1 overflow-y-auto bg-gray-50 focus:outline-none">
                    <div class="py-6 px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>