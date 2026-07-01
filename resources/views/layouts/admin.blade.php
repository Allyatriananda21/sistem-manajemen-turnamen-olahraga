<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 antialiased font-sans flex flex-col md:flex-row" x-data="{ mobileSidebarOpen: false }">
        
        <!-- Sidebar Navigation -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen md:pl-64">
            <!-- Top Navbar -->
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <!-- Burger button for mobile -->
                    <button @click="mobileSidebarOpen = true" class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
                        {{ $title ?? 'Admin Dashboard' }}
                    </h2>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- User Info / Avatar with Logout Dropdown -->
                    @auth
                        <div x-data="{ open: false }" class="relative flex items-center gap-3">
                            <!-- Name & Role (hidden on mobile) -->
                            <div class="flex flex-col text-right hidden sm:flex">
                                <span class="text-sm font-semibold text-slate-900 dark:text-white leading-none">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 capitalize mt-1 tracking-wider">{{ auth()->user()->role ?? 'Admin' }}</span>
                            </div>

                            <!-- Avatar button (click to toggle dropdown) -->
                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-md select-none shrink-0 hover:ring-2 hover:ring-indigo-400 focus:outline-none transition-all duration-200"
                            >
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </button>

                            <!-- Dropdown menu -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.outside="open = false"
                                class="absolute right-0 top-12 z-50 w-48 rounded-xl bg-white dark:bg-slate-900 shadow-lg ring-1 ring-slate-200/80 dark:ring-slate-700/80 py-1"
                                style="display: none;"
                            >
                                <!-- User info in dropdown -->
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                </div>

                                <!-- Logout form -->
                                <form method="POST" action="{{ route('logout') }}" class="px-2 py-1">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150 cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 md:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
        @stack('scripts')
    </body>
</html>
