<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.matches') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Pertandingan</a>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.matches.control', $match) }}" wire:navigate class="hover:text-indigo-600 transition-colors">{{ $match->team1->name }} vs {{ $match->team2->name }}</a>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-slate-700 dark:text-slate-300">Statistik</span>
    </div>

    {{-- Match Info --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Statistik Pertandingan</flux:heading>
            <flux:text class="mt-1 text-slate-500">
                {{ $match->team1->name }} vs {{ $match->team2->name }}
                @if ($match->round)
                    <span class="mx-1 text-slate-300">·</span>
                    <span>{{ $match->round }}</span>
                @endif
            </flux:text>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 px-5 py-2 text-center shadow-md">
                <span class="text-2xl font-bold text-white tabular-nums">
                    {{ $match->score_team1 }} — {{ $match->score_team2 }}
                </span>
            </div>
            <flux:badge color="green" size="lg">Done</flux:badge>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- LEFT: Input Form --}}
        <div class="lg:col-span-2">
            <flux:card class="space-y-5">
                <flux:heading size="lg">Tambah Statistik</flux:heading>
                <flux:separator />

                <form wire:submit="addStat" class="space-y-4">

                    <flux:field>
                        <flux:label>Pemain</flux:label>
                        @if ($this->players->isEmpty())
                            <flux:text class="text-sm text-amber-600">Belum ada pemain terdaftar di kedua tim.</flux:text>
                        @else
                            <select
                                wire:model="formPlayerId"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                            >
                                <option value="">— Pilih Pemain —</option>
                                <optgroup label="{{ $match->team1->name }}">
                                    @foreach ($this->players->where('team_id', $match->team1_id) as $player)
                                        <option value="{{ $player->id }}">
                                            {{ $player->jersey_number ? '#'.$player->jersey_number.' ' : '' }}{{ $player->full_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ $match->team2->name }}">
                                    @foreach ($this->players->where('team_id', $match->team2_id) as $player)
                                        <option value="{{ $player->id }}">
                                            {{ $player->jersey_number ? '#'.$player->jersey_number.' ' : '' }}{{ $player->full_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        @endif
                        <flux:error name="formPlayerId" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Tipe Statistik</flux:label>
                        <select
                            wire:model="formStatType"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                        >
                            @foreach (\App\Livewire\Admin\MatchStatisticsInput::$statLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <flux:error name="formStatType" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Menit <span class="text-slate-400 font-normal">(opsional)</span></flux:label>
                        <flux:input
                            wire:model="formMinute"
                            type="number"
                            min="1"
                            max="200"
                            placeholder="cth. 45"
                            inputmode="numeric"
                        />
                        <flux:description>Kosongkan untuk MVP atau kejadian tanpa menit spesifik.</flux:description>
                        <flux:error name="formMinute" />
                    </flux:field>

                    <flux:button
                        type="submit"
                        variant="primary"
                        icon="plus"
                        class="w-full"
                        :disabled="$this->players->isEmpty()"
                    >
                        Tambah
                    </flux:button>
                </form>
            </flux:card>
        </div>

        {{-- RIGHT: Statistics List --}}
        <div class="lg:col-span-3">
            <flux:card class="space-y-4">
                <flux:heading size="lg">
                    Statistik Tercatat
                    @if ($this->statistics->isNotEmpty())
                        <flux:badge color="zinc" class="ml-2">{{ $this->statistics->count() }}</flux:badge>
                    @endif
                </flux:heading>
                <flux:separator />

                @if ($this->statistics->isEmpty())
                    <div class="flex flex-col items-center gap-2 py-10 text-center">
                        <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <flux:text class="text-slate-400">Belum ada statistik. Gunakan form di kiri untuk menambahkan.</flux:text>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($this->statistics as $stat)
                            <div wire:key="stat-{{ $stat->id }}" class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    {{-- Stat icon / minute --}}
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold dark:bg-slate-800">
                                        @if ($stat->minute)
                                            <span class="text-xs text-slate-600 dark:text-slate-400">{{ $stat->minute }}'</span>
                                        @else
                                            <span class="text-base">{{ ['goal'=>'⚽','assist'=>'🅰️','yellow_card'=>'🟨','red_card'=>'🟥','mvp'=>'⭐'][$stat->stat_type] ?? '•' }}</span>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $stat->player->full_name }}
                                            @if ($stat->player->jersey_number)
                                                <span class="text-xs text-slate-400">#{{ $stat->player->jersey_number }}</span>
                                            @endif
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-500">{{ \App\Livewire\Admin\MatchStatisticsInput::$statLabels[$stat->stat_type] }}</span>
                                            <span class="text-xs text-slate-300">·</span>
                                            <span class="text-xs text-slate-500">{{ $stat->player->team->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <flux:button
                                    wire:click="deleteStat({{ $stat->id }})"
                                    wire:confirm="Hapus statistik ini?"
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                />
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>

    </div>

    {{-- Back --}}
    <flux:button href="{{ route('admin.matches.control', $match) }}" wire:navigate variant="ghost" icon="arrow-left">
        Kembali ke Kontrol Pertandingan
    </flux:button>

</div>
