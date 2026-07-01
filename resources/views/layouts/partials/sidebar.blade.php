<!-- Mobile Sidebar Backdrop -->
<div x-show="mobileSidebarOpen" 
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="mobileSidebarOpen = false" 
     class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs md:hidden"
     style="display: none;">
</div>

<!-- Sidebar Container -->
<aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed top-0 bottom-0 left-0 z-50 w-64 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-r border-slate-200/80 dark:border-slate-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out md:z-30">
    
    <!-- Sidebar Header (Logo) -->
    <div>
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200/60 dark:border-slate-800/60">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group" wire:navigate>
                <div class="p-2 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-xl text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-all duration-300">
                    <!-- Modern sport/trophy icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m12 3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm leading-tight text-slate-950 dark:text-white tracking-wide">TrophyHub</span>
                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold tracking-widest uppercase">Admin Panel</span>
                </div>
            </a>
            
            <!-- Close button for mobile -->
            <button @click="mobileSidebarOpen = false" class="md:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="px-4 py-6 space-y-1.5 overflow-y-auto">
            <!-- Dashboard (Active) -->
            <a href="{{ route('admin.dashboard') }}" 
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group 
               {{ request()->routeIs('admin.dashboard') 
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs' 
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <!-- Tim (admin only) -->
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.teams') }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
               {{ request()->routeIs('admin.teams')
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-sm">Tim</span>
            </a>
            @endif

            <!-- Jadwal (admin only) -->
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.fixtures') }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
               {{ request()->routeIs('admin.fixtures')
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Jadwal</span>
            </a>
            @endif

            <!-- Pertandingan (admin + wasit) -->
            @if(auth()->user()->isAdmin() || auth()->user()->isWasit())
            <a href="{{ route('admin.matches') }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
               {{ request()->routeIs('admin.matches')
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Pertandingan</span>
            </a>
            @endif

            <!-- Bracket (admin only) -->
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.bracket') }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
               {{ request()->routeIs('admin.bracket')
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span class="text-sm">Bracket</span>
            </a>
            @endif

            <!-- Klasemen (admin + wasit) -->
            @if(auth()->user()->isAdmin() || auth()->user()->isWasit())
            <a href="{{ route('admin.standings') }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
               {{ request()->routeIs('admin.standings')
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-sm">Klasemen</span>
            </a>
            @endif

            <!-- POS (admin + kasir) -->
            @if(auth()->user()->isAdmin() || auth()->user()->isKasir())
            <a href="{{ route('admin.pos') }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
               {{ request()->routeIs('admin.pos') && ! request()->routeIs('admin.pos.*')
                  ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-sm">Kasir POS</span>
            </a>
            @endif

            {{-- Kelola Produk (admin only) --}}
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.pos.products') }}"
                   wire:navigate
                   class="flex items-center gap-3 px-4 py-2.5 ml-4 rounded-xl transition-all duration-300 group text-sm
                   {{ request()->routeIs('admin.pos.products')
                      ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                      : 'text-slate-500 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Kelola Produk</span>
                </a>
            @endif

            <!-- Laporan (admin only) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.laporan') }}"
                   wire:navigate
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
                   {{ request()->routeIs('admin.laporan')
                      ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm">Laporan</span>
                </a>
            @endif

            <!-- Galeri (admin only) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.gallery') }}"
                   wire:navigate
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
                   {{ request()->routeIs('admin.gallery')
                      ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm">Galeri Foto</span>
                </a>
            @endif

            <!-- Manajemen User (admin only) -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users') }}"
                   wire:navigate
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group
                   {{ request()->routeIs('admin.users')
                      ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-l-4 border-indigo-600 font-semibold shadow-xs'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-indigo-600 dark:hover:text-indigo-400 hover:translate-x-1' }}">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-sm">Manajemen User</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- Sidebar Footer (User Menu & Logout) -->
    <div class="p-4 border-t border-slate-200/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/50">
        @auth
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-xs shrink-0 select-none">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex flex-col overflow-hidden text-left">
                        <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="shrink-0 flex">
                    @csrf
                    <button type="submit" 
                            class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200 cursor-pointer"
                            title="Log out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex flex-col gap-2">
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all duration-200">
                    Log In
                </a>
            </div>
        @endauth
    </div>
</aside>
