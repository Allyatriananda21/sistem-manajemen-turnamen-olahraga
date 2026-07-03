<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Daftar Pertandingan</flux:heading>
            <flux:text class="mt-1 text-slate-500">Kelola venue, jadwal, dan wasit tiap pertandingan.</flux:text>
        </div>
        <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="ghost" icon="bolt" size="sm">
            Generator Jadwal
        </flux:button>
    </div>

    {{-- Filters --}}
    <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama tim..."
                icon="magnifying-glass"
                class="sm:max-w-xs"
            />
            <select wire:model.live="statusFilter" class="sm:w-44 w-full rounded-lg border border-[#c8e87d] bg-[#E4FD97] dark:bg-[#2a3d1a] px-3 py-2 text-sm text-black focus:outline-none focus:ring-2 focus:ring-[#4a7c30]">
                <option value="all">Semua Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="done">Done</option>
                <option value="cancelled">Cancelled</option>
            </select>
            @if ($search || $statusFilter !== 'all')
                <flux:badge color="blue" class="self-start sm:self-auto">
                    {{ $matches->total() }} hasil
                </flux:badge>
            @endif
        </div>
    </flux:card>

    {{-- Match Cards --}}
    <div class="space-y-3">
    @forelse ($matches as $match)
        @php
            $isBye       = $match->notes && str_contains($match->notes, 'BYE');
            $isDone      = $match->status === 'done';
            $isOngoing   = $match->status === 'ongoing';
            $isCancelled = $match->status === 'cancelled';

            $borderColor = match(true) {
                $isOngoing   => 'border-blue-400 dark:border-blue-500',
                $isDone      => 'border-green-400 dark:border-green-600',
                $isCancelled => 'border-red-400 dark:border-red-600',
                default      => 'border-[#c8e87d] dark:border-[rgba(228,253,151,0.25)]',
            };
        @endphp

        <div wire:key="match-{{ $match->id }}"
             class="rounded-2xl border-2 bg-[#E4FD97] dark:bg-[#2a3d1a] shadow-sm transition-all duration-150 hover:shadow-md {{ $borderColor }}">

            {{-- Card body --}}
            <div class="flex items-stretch gap-0">

                {{-- ── MAIN: VS Area ── --}}
                <div class="flex flex-1 items-center gap-4 px-5 py-4">

                    @if ($isBye)
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-xs font-bold text-white shadow-sm select-none">
                            {{ strtoupper(substr($match->team1->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-[#1e2b1d] dark:text-white">{{ $match->team1->name }}</p>
                            <flux:badge color="amber" size="sm" class="mt-1">BYE</flux:badge>
                        </div>
                    @else
                        {{-- Team 1 --}}
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-xs font-bold text-white shadow-sm select-none">
                                {{ strtoupper(substr($match->team1->name, 0, 2)) }}
                            </div>
                            <span class="truncate font-semibold text-[#1e2b1d] dark:text-white">
                                {{ $match->team1->name }}
                            </span>
                        </div>

                        {{-- Center: VS / Score --}}
                        <div class="flex shrink-0 flex-col items-center justify-center gap-0.5 px-2">
                            @if ($isDone || $isOngoing)
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xl font-black tabular-nums leading-none text-[#1e2b1d] dark:text-white">
                                        {{ $match->score_team1 }}
                                    </span>
                                    <span class="text-xs font-bold text-[#4a7c30]/40 dark:text-[#E4FD97]/30">:</span>
                                    <span class="text-xl font-black tabular-nums leading-none text-[#1e2b1d] dark:text-white">
                                        {{ $match->score_team2 }}
                                    </span>
                                </div>
                                @if ($isOngoing)
                                    <span class="animate-pulse text-[9px] font-extrabold uppercase tracking-widest text-blue-500">● Live</span>
                                @endif
                            @else
                                <span class="rounded-md bg-[#1e2b1d]/8 dark:bg-white/8 px-2.5 py-0.5 text-[11px] font-black uppercase tracking-widest text-[#1e2b1d]/40 dark:text-white/40">
                                    VS
                                </span>
                            @endif
                        </div>

                        {{-- Team 2 --}}
                        <div class="flex min-w-0 flex-1 items-center justify-end gap-3">
                            <span class="truncate text-right font-semibold text-[#1e2b1d] dark:text-white">
                                {{ $match->team2->name }}
                            </span>
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-pink-500 to-rose-600 text-xs font-bold text-white shadow-sm select-none">
                                {{ strtoupper(substr($match->team2->name, 0, 2)) }}
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ── DIVIDER ── --}}
                <div class="w-px self-stretch bg-[#c8e87d]/60 dark:bg-[rgba(228,253,151,0.12)]"></div>

                {{-- ── RIGHT: Meta + Actions ── --}}
                <div class="flex w-56 shrink-0 flex-col justify-between gap-3 px-4 py-4">

                    {{-- Badges row --}}
                    <div class="flex flex-wrap items-center gap-1.5">
                        <x-status-badge :status="$match->status" type="match" size="sm" />
                        @if ($match->round)
                            <flux:badge color="zinc" size="sm">{{ $match->round }}</flux:badge>
                        @endif
                    </div>

                    {{-- Meta details --}}
                    <div class="space-y-1">
                        @if ($match->match_date)
                            <div class="flex items-center gap-1.5 text-xs text-[#1e2b1d]/60 dark:text-slate-400">
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="whitespace-nowrap">{{ $match->match_date->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if ($match->venue)
                            <div class="flex items-center gap-1.5 text-xs text-[#1e2b1d]/60 dark:text-slate-400">
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate">{{ $match->venue }}</span>
                            </div>
                        @endif
                        @if ($match->referee)
                            <div class="flex items-center gap-1.5 text-xs text-[#1e2b1d]/60 dark:text-slate-400">
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="truncate">{{ $match->referee }}</span>
                            </div>
                        @endif
                        @if (! $match->match_date && ! $match->venue && ! $match->referee)
                            <p class="text-xs italic text-[#1e2b1d]/30 dark:text-white/20">Belum ada detail</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5">
                        @if (! $isBye)
                            <flux:button
                                href="{{ route('admin.matches.control', $match) }}"
                                wire:navigate
                                size="sm"
                                variant="primary"
                                icon="play"
                                class="flex-1"
                            >
                                Kelola
                            </flux:button>
                        @endif
                        <flux:button
                            wire:click="openEdit({{ $match->id }})"
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            title="Edit Detail"
                        />
                    </div>

                </div>
            </div>
        </div>

    @empty
        <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-16">
            <div class="flex flex-col items-center gap-3 text-center">
                <svg class="h-12 w-12 text-[#4a7c30]/30 dark:text-[#E4FD97]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <flux:text class="text-[#1e2b1d] dark:text-[#E4FD97]/70">
                    {{ $search || $statusFilter !== 'all' ? 'Tidak ada pertandingan yang cocok.' : 'Belum ada pertandingan. Generate jadwal terlebih dahulu.' }}
                </flux:text>
                @if (! $search && $statusFilter === 'all')
                    <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="ghost" size="sm" icon="bolt">
                        Generator Jadwal
                    </flux:button>
                @endif
            </div>
        </flux:card>
    @endforelse
    </div>

    {{-- Pagination --}}
    @if ($matches->hasPages())
        <div class="flex justify-center">
            {{ $matches->links() }}
        </div>
    @endif
    @if ($matches->total() > 0)
        <flux:text class="text-center text-xs text-slate-400">
            Menampilkan {{ $matches->firstItem() }}–{{ $matches->lastItem() }} dari {{ $matches->total() }} pertandingan
        </flux:text>
    @endif

    {{-- Edit Detail Modal --}}
    <flux:modal wire:model.self="showEditModal" class="md:w-[28rem]">
        <form wire:submit="saveEdit" class="space-y-5">
            <div>
                <flux:heading size="lg">Edit Detail Pertandingan</flux:heading>
                <flux:text class="mt-1 text-slate-500">Update venue, waktu, dan wasit. Skor & status dikelola di halaman Live Score.</flux:text>
            </div>
            <flux:field>
                <flux:label>Venue / Lapangan</flux:label>
                <flux:input wire:model="editVenue" type="text" placeholder="cth. Lapangan A, GOR Serbaguna" maxlength="200"/>
                <flux:error name="editVenue"/>
            </flux:field>
            <flux:field>
                <flux:label>Tanggal & Waktu</flux:label>
                <flux:input wire:model="editMatchDate" type="datetime-local"/>
                <flux:error name="editMatchDate"/>
            </flux:field>
            <flux:field>
                <flux:label>Nama Wasit</flux:label>
                <flux:input wire:model="editReferee" type="text" placeholder="cth. Budi Santoso" maxlength="100"/>
                <flux:error name="editReferee"/>
            </flux:field>
            <div class="flex justify-end gap-3 pt-1">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
