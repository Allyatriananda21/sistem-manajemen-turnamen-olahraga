<!-- Mobile Sidebar Backdrop -->
<div x-show="mobileSidebarOpen"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="mobileSidebarOpen = false"
     class="fixed inset-0 z-50 md:hidden"
     style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: none;">
</div>

<!-- Sidebar Container -->
<aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed top-0 bottom-0 left-0 z-50 w-52 flex flex-col justify-between transition-transform duration-300 ease-in-out md:z-30"
       style="background: rgba(30,43,29,0.97); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-right: 1px solid rgba(255,255,255,0.07);">

    <!-- Sidebar Header -->
    <div class="flex-1 overflow-y-auto">
        <div class="h-16 flex items-center justify-between px-5" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group" wire:navigate>
                <!-- Trophy icon with accent color -->
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-all duration-300 group-hover:scale-105"
                     style="background: rgba(228,253,151,0.1); border: 1px solid rgba(228,253,151,0.2);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #E4FD97;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m12 3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm leading-tight tracking-wide" style="font-family: 'Space Grotesk', sans-serif; color: #f8fafc;">TrophyHub</span>
                    <span class="text-[10px] font-semibold tracking-widest uppercase" style="color: #E4FD97;">Admin Panel</span>
                </div>
            </a>

            <!-- Close button mobile -->
            <button @click="mobileSidebarOpen = false"
                    class="md:hidden p-1.5 rounded-lg focus:outline-none transition-colors duration-200"
                    style="color: #64748b;"
                    onmouseover="this.style.background='rgba(255,255,255,0.07)'"
                    onmouseout="this.style.background='transparent'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="px-3 py-5 space-y-0.5">

            @php
            $navItemBase = "flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 group w-full text-sm font-medium";
            $navItemActive = "font-semibold";
            @endphp

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.dashboard') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.dashboard')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.dashboard') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.dashboard') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Tim --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.teams') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.teams*') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.teams*')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.teams*') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.teams*') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Tim</span>
            </a>
            @endif

            {{-- Jadwal --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.fixtures') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.fixtures') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.fixtures')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.fixtures') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.fixtures') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Jadwal</span>
            </a>
            @endif

            {{-- Pertandingan --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isWasit())
            <a href="{{ route('admin.matches') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.matches') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.matches')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.matches') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.matches') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Pertandingan</span>
            </a>
            @endif

            {{-- Bracket --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.bracket') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.bracket') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.bracket')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.bracket') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.bracket') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span>Bracket</span>
            </a>
            @endif

            {{-- Klasemen --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isWasit())
            <a href="{{ route('admin.standings') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.standings') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.standings')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.standings') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.standings') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Klasemen</span>
            </a>
            @endif

            {{-- Divider --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isKasir())
            <div class="pt-3 pb-1 px-1">
                <span class="text-[10px] font-bold uppercase tracking-widest" style="color: rgba(228,253,151,0.4);">Point of Sale</span>
            </div>
            @endif

            {{-- POS Kasir --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isKasir())
            <a href="{{ route('admin.pos') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.pos') && !request()->routeIs('admin.pos.*') ? $navItemActive : '' }}"
               style="{{ (request()->routeIs('admin.pos') && !request()->routeIs('admin.pos.*'))
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !(request()->routeIs('admin.pos') && !request()->routeIs('admin.pos.*')) ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !(request()->routeIs('admin.pos') && !request()->routeIs('admin.pos.*')) ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Kasir POS</span>
            </a>
            @endif

            {{-- Kelola Produk --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.pos.products') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.pos.products') ? $navItemActive : '' }} ml-4 text-xs"
               style="{{ request()->routeIs('admin.pos.products')
                   ? 'background: rgba(228,253,151,0.08); color: #E4FD97; border-left: 3px solid rgba(228,253,151,0.5); padding-left: calc(0.875rem - 3px);'
                   : 'color: #64748b;' }}"
               onmouseover="{{ !request()->routeIs('admin.pos.products') ? "this.style.background='rgba(255,255,255,0.04)'; this.style.color='#94a3b8';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.pos.products') ? "this.style.background='transparent'; this.style.color='#64748b';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Kelola Produk</span>
            </a>
            @endif

            {{-- Divider --}}
            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-1">
                <span class="text-[10px] font-bold uppercase tracking-widest" style="color: rgba(228,253,151,0.4);">Manajemen</span>
            </div>
            @endif

            {{-- Laporan --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.laporan') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.laporan') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.laporan')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.laporan') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.laporan') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Laporan</span>
            </a>
            @endif


            {{-- Galeri --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.gallery') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.gallery') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.gallery')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.gallery') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.gallery') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Galeri Foto</span>
            </a>
            @endif

            {{-- Manajemen User --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.users') }}"
               wire:navigate
               class="{{ $navItemBase }} {{ request()->routeIs('admin.users') ? $navItemActive : '' }}"
               style="{{ request()->routeIs('admin.users')
                   ? 'background: rgba(228,253,151,0.1); color: #E4FD97; border-left: 3px solid #E4FD97; padding-left: calc(0.875rem - 3px);'
                   : 'color: #94a3b8;' }}"
               onmouseover="{{ !request()->routeIs('admin.users') ? "this.style.background='rgba(255,255,255,0.05)'; this.style.color='#f8fafc';" : '' }}"
               onmouseout="{{ !request()->routeIs('admin.users') ? "this.style.background='transparent'; this.style.color='#94a3b8';" : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Manajemen User</span>
            </a>
            @endif

        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4" style="border-top: 1px solid rgba(255,255,255,0.07);">
        @auth
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 overflow-hidden min-w-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shrink-0 select-none"
                         style="background: linear-gradient(135deg, #2D3E2C 0%, #3d5c3b 100%); color: #E4FD97; border: 1px solid rgba(228,253,151,0.25); font-family: 'Space Grotesk', sans-serif;">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex flex-col overflow-hidden min-w-0">
                        <span class="text-sm font-semibold truncate" style="color: #f8fafc; font-family: 'Space Grotesk', sans-serif;">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="p-2 rounded-lg transition-colors duration-200 cursor-pointer"
                            style="color: #475569;"
                            title="Log out"
                            onmouseover="this.style.background='rgba(248,113,113,0.1)'; this.style.color='#f87171';"
                            onmouseout="this.style.background='transparent'; this.style.color='#475569';">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}"
               class="flex items-center justify-center w-full px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200"
               style="background: #E4FD97; color: #2D3E2C; font-family: 'Space Grotesk', sans-serif;">
                Log In
            </a>
        @endauth
    </div>
</aside>
