<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body class="min-h-screen antialiased flex flex-col md:flex-row" 
          style="background-color: #1e2b1d; color: #f8fafc;"
          x-data="{ mobileSidebarOpen: false }">

        <!-- Ambient background glows -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute top-0 left-1/4 w-[500px] h-[500px] rounded-full blur-[160px]" style="background: rgba(45,62,44,0.6);"></div>
            <div class="absolute bottom-1/4 right-0 w-[400px] h-[400px] rounded-full blur-[140px]" style="background: rgba(228,253,151,0.04);"></div>
        </div>

        <!-- Sidebar Navigation -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <div class="relative z-10 flex-1 flex flex-col min-h-screen md:pl-64">

            <!-- Top Navbar -->
            <header class="sticky top-0 z-30 flex items-center justify-between px-6 py-4 border-b"
                    style="background: rgba(30,43,29,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-color: rgba(255,255,255,0.07);">
                <div class="flex items-center gap-4">
                    <!-- Burger button for mobile -->
                    <button @click="mobileSidebarOpen = true"
                            class="md:hidden p-2 rounded-lg transition-colors duration-200"
                            style="color: #94a3b8;"
                            onmouseover="this.style.background='rgba(255,255,255,0.07)'"
                            onmouseout="this.style.background='transparent'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-base font-bold tracking-tight" style="font-family: 'Space Grotesk', sans-serif; color: #f8fafc;">
                        {{ $title ?? 'Admin Dashboard' }}
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <div x-data="{ open: false }" class="relative flex items-center gap-3">
                            <!-- Name & Role -->
                            <div class="hidden sm:flex flex-col text-right">
                                <span class="text-sm font-semibold leading-none" style="color: #f8fafc; font-family: 'Space Grotesk', sans-serif;">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] capitalize mt-1 tracking-widest" style="color: #E4FD97;">{{ auth()->user()->role ?? 'Admin' }}</span>
                            </div>

                            <!-- Avatar -->
                            <button
                                @click="open = !open"
                                @keydown.escape.window="open = false"
                                class="w-9 h-9 rounded-full text-sm font-bold shadow-lg select-none shrink-0 transition-all duration-200 focus:outline-none"
                                style="background: linear-gradient(135deg, #2D3E2C 0%, #3d5c3b 100%); color: #E4FD97; border: 1.5px solid rgba(228,253,151,0.3); font-family: 'Space Grotesk', sans-serif;"
                            >
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </button>

                            <!-- Dropdown -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.outside="open = false"
                                class="absolute right-0 top-12 z-50 w-52 rounded-2xl py-1 shadow-2xl"
                                style="background: #1e2b1d; border: 1px solid rgba(255,255,255,0.08); display: none;"
                            >
                                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <p class="text-sm font-semibold truncate" style="color: #f8fafc; font-family: 'Space Grotesk', sans-serif;">{{ auth()->user()->name }}</p>
                                    <p class="text-xs truncate mt-0.5" style="color: #64748b;">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="px-2 py-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition-colors duration-150 cursor-pointer"
                                            style="color: #f87171;"
                                            onmouseover="this.style.background='rgba(248,113,113,0.08)'"
                                            onmouseout="this.style.background='transparent'">
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
