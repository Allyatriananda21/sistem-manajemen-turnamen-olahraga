<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Klasemen</flux:heading>
            <flux:text class="mt-1 text-slate-500">Peringkat tim berdasarkan poin dan selisih gol.</flux:text>
        </div>

        {{-- Round / Pool filter --}}
        @if ($this->availableRounds->isNotEmpty())
            <select wire:model.live="roundFilter" class="sm:w-52 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Babak</option>
                @foreach ($this->availableRounds as $round)
                    <option :value="$round">{{ $round }}</option>
                @endforeach
            </select>
        @endif
    </div>

    {{-- Active filter badge --}}
    @if ($roundFilter !== '')
        <div class="flex items-center gap-2">
            <flux:badge color="blue">{{ $roundFilter }}</flux:badge>
            <flux:button wire:click="$set('roundFilter', '')" size="sm" variant="ghost" icon="x-mark">
                Hapus filter
            </flux:button>
        </div>
    @endif

    {{-- Standings Table --}}
    <flux:card class="overflow-hidden p-0">
        <flux:table>
            <flux:columns>
                <flux:column class="w-12 text-center">#</flux:column>
                <flux:column>Tim</flux:column>
                <flux:column class="text-center">M</flux:column>
                <flux:column class="text-center">W</flux:column>
                <flux:column class="text-center">D</flux:column>
                <flux:column class="text-center">L</flux:column>
                <flux:column class="text-center">GD</flux:column>
                <flux:column class="text-center font-bold">PTS</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($this->standings as $rank => $standing)
                    @php $position = $rank + 1; @endphp
                    <flux:row wire:key="standing-{{ $standing->id }}"
                        @class([
                            'border-l-4 border-indigo-500 bg-indigo-50/40 dark:bg-indigo-900/10' => $position === 1,
                        ])
                    >
                        {{-- Rank --}}
                        <flux:cell class="text-center">
                            @if ($position === 1)
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white shadow-sm">
                                    1
                                </span>
                            @elseif ($position === 2)
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-300 dark:bg-slate-600 text-xs font-bold text-slate-700 dark:text-slate-200">
                                    2
                                </span>
                            @elseif ($position === 3)
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-amber-600/70 text-xs font-bold text-white">
                                    3
                                </span>
                            @else
                                <span class="text-sm text-slate-400">{{ $position }}</span>
                            @endif
                        </flux:cell>

                        {{-- Tim --}}
                        <flux:cell>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-xs font-bold text-white shadow-sm select-none">
                                    {{ strtoupper(substr($standing->team->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $standing->team->name }}
                                </span>
                            </div>
                        </flux:cell>

                        {{-- Played --}}
                        <flux:cell class="text-center">
                            <span class="text-sm tabular-nums text-slate-600 dark:text-slate-400">{{ $standing->played }}</span>
                        </flux:cell>

                        {{-- Win --}}
                        <flux:cell class="text-center">
                            <span class="text-sm tabular-nums font-medium text-green-600 dark:text-green-400">{{ $standing->win }}</span>
                        </flux:cell>

                        {{-- Draw --}}
                        <flux:cell class="text-center">
                            <span class="text-sm tabular-nums text-slate-500">{{ $standing->draw }}</span>
                        </flux:cell>

                        {{-- Lose --}}
                        <flux:cell class="text-center">
                            <span class="text-sm tabular-nums font-medium text-red-500 dark:text-red-400">{{ $standing->lose }}</span>
                        </flux:cell>

                        {{-- Goal Diff --}}
                        <flux:cell class="text-center">
                            @php $gd = $standing->goal_diff; @endphp
                            <span @class([
                                'text-sm tabular-nums font-medium',
                                'text-green-600 dark:text-green-400' => $gd > 0,
                                'text-red-500 dark:text-red-400'     => $gd < 0,
                                'text-slate-400'                     => $gd === 0,
                            ])>
                                {{ $gd > 0 ? '+' . $gd : $gd }}
                            </span>
                        </flux:cell>

                        {{-- Points --}}
                        <flux:cell class="text-center">
                            <span @class([
                                'inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-sm font-bold tabular-nums',
                                'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' => $position === 1,
                                'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200' => $position !== 1,
                            ])>
                                {{ $standing->points }}
                            </span>
                        </flux:cell>

                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <flux:text class="text-slate-400">
                                    {{ $roundFilter ? 'Belum ada pertandingan selesai di babak ini.' : 'Klasemen belum tersedia. Selesaikan pertandingan terlebih dahulu.' }}
                                </flux:text>
                                @if (! $roundFilter)
                                    <flux:button href="{{ route('admin.matches') }}" wire:navigate variant="ghost" size="sm">
                                        Lihat Pertandingan
                                    </flux:button>
                                @endif
                            </div>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>
    </flux:card>

    {{-- Legend --}}
    @if ($this->standings->isNotEmpty())
        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400">
            <span><strong class="text-slate-500">M</strong> = Main</span>
            <span><strong class="text-slate-500">W</strong> = Menang</span>
            <span><strong class="text-slate-500">D</strong> = Seri</span>
            <span><strong class="text-slate-500">L</strong> = Kalah</span>
            <span><strong class="text-slate-500">GD</strong> = Selisih Gol</span>
            <span><strong class="text-slate-500">PTS</strong> = Poin</span>
        </div>
    @endif

</div>
