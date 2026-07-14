<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Klasemen</flux:heading>
            <flux:text class="mt-1 text-slate-500">Peringkat tim berdasarkan poin dan selisih gol.</flux:text>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            {{-- Sport filter --}}
            @if ($this->availableSports->isNotEmpty())
                <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-2">
                    <select wire:model.live="sportFilter" class="w-full rounded-lg border border-[#c8e87d] bg-[#E4FD97] dark:bg-[#2a3d1a] px-3 py-1.5 text-sm text-black focus:outline-none focus:ring-2 focus:ring-[#4a7c30] sm:w-52">
                        <option value="">Semua Cabang Olahraga</option>
                        @foreach ($this->availableSports as $sport)
                            <option value="{{ $sport }}">{{ $sport }}</option>
                        @endforeach
                    </select>
                </flux:card>
            @endif

            {{-- Round filter --}}
            @if ($this->availableRounds->isNotEmpty())
                <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-2">
                    <select wire:model.live="roundFilter" class="w-full rounded-lg border border-[#c8e87d] bg-[#E4FD97] dark:bg-[#2a3d1a] px-3 py-1.5 text-sm text-black focus:outline-none focus:ring-2 focus:ring-[#4a7c30] sm:w-48">
                        <option value="">Semua Babak</option>
                        @foreach ($this->availableRounds as $round)
                            <option value="{{ $round }}">{{ $round }}</option>
                        @endforeach
                    </select>
                </flux:card>
            @endif
        </div>
    </div>

    {{-- Active filter badges --}}
    @if ($sportFilter || $roundFilter !== '')
        <div class="flex flex-wrap items-center gap-2">
            @if ($sportFilter)
                <flux:badge color="indigo">{{ $sportFilter }}</flux:badge>
                <flux:button wire:click="$set('sportFilter', '')" size="sm" variant="ghost" icon="x-mark">
                    Hapus cabang
                </flux:button>
            @endif
            @if ($roundFilter !== '')
                <flux:badge color="blue">{{ $roundFilter }}</flux:badge>
                <flux:button wire:click="$set('roundFilter', '')" size="sm" variant="ghost" icon="x-mark">
                    Hapus babak
                </flux:button>
            @endif
        </div>
    @endif

    @if ($this->standings->isEmpty())
        {{-- Empty state --}}
        <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-16">
            <div class="flex flex-col items-center gap-3 text-center">
                <svg class="h-12 w-12 text-[#4a7c30]/30 dark:text-[#E4FD97]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <flux:text class="text-[#1e2b1d] dark:text-[#E4FD97]/70">
                    {{ $roundFilter ? 'Belum ada pertandingan selesai di babak ini.' : 'Klasemen belum tersedia. Selesaikan pertandingan terlebih dahulu.' }}
                </flux:text>
                @if (! $roundFilter)
                    <flux:button href="{{ route('admin.matches') }}" wire:navigate variant="ghost" size="sm">
                        Lihat Pertandingan
                    </flux:button>
                @endif
            </div>
        </flux:card>
    @else

        {{-- ── TOP 3 PODIUM ── --}}
        @if ($this->standings->count() >= 1)
            @php
                $top = $this->standings->take(3);
                $medals = [
                    1 => ['bg' => 'bg-amber-400',       'text' => 'text-white',       'ring' => 'ring-amber-400',   'label' => '🥇', 'size' => 'h-14 w-14', 'order' => 'order-2 sm:scale-105'],
                    2 => ['bg' => 'bg-slate-300 dark:bg-slate-500', 'text' => 'text-slate-700 dark:text-white', 'ring' => 'ring-slate-300 dark:ring-slate-500', 'label' => '🥈', 'size' => 'h-12 w-12', 'order' => 'order-1'],
                    3 => ['bg' => 'bg-amber-600/70',     'text' => 'text-white',       'ring' => 'ring-amber-600/70', 'label' => '🥉', 'size' => 'h-12 w-12', 'order' => 'order-3'],
                ];
            @endphp
            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                @foreach ($top as $rank => $standing)
                    @php
                        $pos    = $rank + 1;
                        $medal  = $medals[$pos];
                    @endphp
                    <div class="flex flex-col items-center gap-2 rounded-2xl border-2 bg-[#E4FD97] p-4 text-center shadow-sm dark:bg-[#2a3d1a]
                        {{ $pos === 1 ? 'border-amber-400 dark:border-amber-500' : ($pos === 2 ? 'border-slate-300 dark:border-slate-500' : 'border-amber-600/50') }}
                        {{ $medal['order'] }}">

                        {{-- Medal emoji --}}
                        <span class="text-2xl leading-none">{{ $medal['label'] }}</span>

                        {{-- Avatar --}}
                        @php
                            $podiumLogoUrl = $standing->team->logo
                                ? (str_starts_with($standing->team->logo, 'http://') || str_starts_with($standing->team->logo, 'https://')
                                    ? $standing->team->logo
                                    : (str_starts_with($standing->team->logo, 'public/')
                                        ? '/storage/' . str_replace('public/', '', $standing->team->logo)
                                        : '/storage/' . $standing->team->logo))
                                : null;
                        @endphp
                        @if ($podiumLogoUrl)
                            <img src="{{ $podiumLogoUrl }}" alt="{{ $standing->team->name }}"
                                 class="{{ $medal['size'] }} shrink-0 rounded-full object-cover shadow-md ring-2 {{ $medal['ring'] }}" />
                        @else
                            <div class="{{ $medal['size'] }} flex shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 font-bold text-white shadow-md ring-2 {{ $medal['ring'] }} select-none
                                {{ $pos === 1 ? 'text-sm' : 'text-xs' }}">
                                {{ strtoupper(substr($standing->team->name, 0, 2)) }}
                            </div>
                        @endif

                        {{-- Name --}}
                        <p class="w-full truncate text-xs font-bold text-[#1e2b1d] dark:text-white sm:text-sm">
                            {{ $standing->team->name }}
                        </p>

                        {{-- Points --}}
                        <div class="rounded-xl px-3 py-1
                            {{ $pos === 1 ? 'bg-amber-400 text-white' : 'bg-[#1e2b1d]/10 text-[#1e2b1d] dark:bg-white/10 dark:text-white' }}">
                            <span class="text-lg font-black tabular-nums leading-none">{{ $standing->points }}</span>
                            <span class="ml-0.5 text-[10px] font-semibold opacity-70">pts</span>
                        </div>

                        {{-- W / D / L --}}
                        <div class="flex items-center gap-2 text-[11px] font-medium">
                            <span class="text-green-600 dark:text-green-400">{{ $standing->win }}W</span>
                            <span class="text-slate-400">{{ $standing->draw }}D</span>
                            <span class="text-red-500">{{ $standing->lose }}L</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── FULL TABLE ── --}}
        <flux:card class="secondary-card overflow-hidden p-0 bg-[#E4FD97] border-[#c8e87d]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[#c8e87d]/60 dark:border-[rgba(228,253,151,0.12)]">
                            <th class="w-10 px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/60 dark:text-[#E4FD97]/40">#</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/60 dark:text-[#E4FD97]/40">Tim</th>
                            <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/60 dark:text-[#E4FD97]/40">Main</th>
                            <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-green-600/70">Menang</th>
                            <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/60 dark:text-[#E4FD97]/40">Seri</th>
                            <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-red-500/70">Kalah</th>
                            <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/60 dark:text-[#E4FD97]/40">+/-</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c8e87d]/40 dark:divide-[rgba(228,253,151,0.08)]">
                        @foreach ($this->standings as $rank => $standing)
                            @php
                                $position = $rank + 1;
                                $gd = $standing->goal_diff;
                            @endphp
                            <tr wire:key="standing-{{ $standing->id }}"
                                class="transition-colors duration-100 hover:bg-[#d8f57a]/30 dark:hover:bg-white/5
                                    {{ $position === 1 ? 'bg-amber-50/60 dark:bg-amber-900/10' : '' }}">

                                {{-- Rank --}}
                                <td class="w-10 px-4 py-3 text-center">
                                    @if ($position === 1)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-[10px] font-black text-white shadow-sm">1</span>
                                    @elseif ($position === 2)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-300 dark:bg-slate-600 text-[10px] font-black text-slate-700 dark:text-white">2</span>
                                    @elseif ($position === 3)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-600/70 text-[10px] font-black text-white">3</span>
                                    @else
                                        <span class="text-xs font-medium text-[#1e2b1d]/40 dark:text-white/30">{{ $position }}</span>
                                    @endif
                                </td>

                                {{-- Tim --}}
                                <td class="px-4 py-3">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        @php
                                            $rowLogoUrl = $standing->team->logo
                                                ? (str_starts_with($standing->team->logo, 'http://') || str_starts_with($standing->team->logo, 'https://')
                                                    ? $standing->team->logo
                                                    : (str_starts_with($standing->team->logo, 'public/')
                                                        ? '/storage/' . str_replace('public/', '', $standing->team->logo)
                                                        : '/storage/' . $standing->team->logo))
                                                : null;
                                        @endphp
                                        @if ($rowLogoUrl)
                                            <img src="{{ $rowLogoUrl }}" alt="{{ $standing->team->name }}"
                                                 class="h-7 w-7 shrink-0 rounded-lg object-cover shadow-sm" />
                                        @else
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 text-[10px] font-bold text-white shadow-sm select-none">
                                                {{ strtoupper(substr($standing->team->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <span class="truncate text-sm font-semibold text-[#1e2b1d] dark:text-white">
                                            {{ $standing->team->name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Main --}}
                                <td class="px-3 py-3 text-center text-sm tabular-nums text-[#1e2b1d]/60 dark:text-slate-400">{{ $standing->played }}</td>

                                {{-- Menang --}}
                                <td class="px-3 py-3 text-center text-sm tabular-nums font-semibold text-green-600 dark:text-green-400">{{ $standing->win }}</td>

                                {{-- Seri --}}
                                <td class="px-3 py-3 text-center text-sm tabular-nums text-[#1e2b1d]/50 dark:text-slate-500">{{ $standing->draw }}</td>

                                {{-- Kalah --}}
                                <td class="px-3 py-3 text-center text-sm tabular-nums font-semibold text-red-500 dark:text-red-400">{{ $standing->lose }}</td>

                                {{-- Selisih Gol --}}
                                <td class="px-3 py-3 text-center text-sm tabular-nums font-medium
                                    {{ $gd > 0 ? 'text-green-600 dark:text-green-400' : ($gd < 0 ? 'text-red-500 dark:text-red-400' : 'text-[#1e2b1d]/40 dark:text-white/30') }}">
                                    {{ $gd > 0 ? '+' . $gd : $gd }}
                                </td>

                                {{-- Poin --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-sm font-bold tabular-nums
                                        {{ $position === 1 ? 'bg-indigo-600 text-white shadow-sm' : 'bg-[#1e2b1d]/10 text-[#1e2b1d] dark:bg-white/10 dark:text-white' }}">
                                        {{ $standing->points }}
                                    </span>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>



    @endif

</div>
