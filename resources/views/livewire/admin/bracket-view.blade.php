<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Bracket Visual</flux:heading>
        <flux:text class="mt-1 text-slate-500">Bagan pertandingan knockout. Klik kotak untuk melihat detail.</flux:text>
    </div>

    @if ($this->rounds->isEmpty())
        {{-- Empty state --}}
        <flux:card class="secondary-card py-16 bg-[#E4FD97] border-[#c8e87d]">
            <div class="flex flex-col items-center gap-4 text-center">
                <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <div>
                    <flux:heading size="lg" class="text-slate-500">Belum ada bracket</flux:heading>
                    <flux:text class="text-slate-400">Generate jadwal format Knockout terlebih dahulu.</flux:text>
                </div>
                <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="primary" icon="bolt">
                    Generator Jadwal
                </flux:button>
            </div>
        </flux:card>
    @else
        {{-- Bracket Tree — horizontal scroll with rounds as columns --}}
        <div class="overflow-x-auto pb-4">
            <div class="flex items-stretch gap-0 min-w-max">

                @foreach ($this->rounds as $roundIndex => $roundData)
                    @php
                        $isLast    = $loop->last;
                        $matchCount = $roundData['matches']->count();
                    @endphp

                    {{-- Round Column --}}
                    <div class="flex flex-col" style="min-width: 170px;">

                        {{-- Round Label --}}
                        <div class="mb-3 px-2">
                            <div class="rounded-lg bg-indigo-600 px-2 py-1 text-center text-xs font-bold uppercase tracking-wider text-white shadow-sm">
                                {{ $roundData['round'] }}
                            </div>
                        </div>

                        {{-- Matches in this round --}}
                        <div class="flex flex-1 flex-col justify-around gap-0">
                            @foreach ($roundData['matches'] as $matchIndex => $match)
                                @php
                                    $isBye    = $match->team1_id === $match->team2_id;
                                    $isDone   = $match->status === 'done';
                                    $isOngoing = $match->status === 'ongoing';

                                    $team1Name   = $match->team1->name ?? '—';
                                    $team2Name   = $isBye ? 'BYE' : ($match->team2->name ?? 'TBD');
                                    $team1IsWinner = $isDone && $match->winner_id === $match->team1_id;
                                    $team2IsWinner = $isDone && $match->winner_id === $match->team2_id;
                                @endphp

                                <div class="relative flex flex-col" style="flex: 1; padding: 12px 0;">

                                    {{-- Connector lines --}}
                                    @if (! $isLast)
                                        {{-- Right connector from this match to next round --}}
                                        <div class="absolute right-0 top-1/2 h-px w-3 -translate-y-1/2 bg-slate-300 dark:bg-slate-600"></div>
                                    @endif

                                    {{-- Match card --}}
                                    <button
                                        wire:click="openMatch({{ $match->id }})"
                                        wire:key="bracket-match-{{ $match->id }}"
                                        class="mx-2 overflow-hidden rounded-lg border shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 text-left w-full
                                            {{ $isOngoing ? 'border-blue-400 dark:border-blue-600' : 'border-slate-200 dark:border-slate-700' }}"
                                    >
                                        {{-- Status indicator stripe --}}
                                        <div class="h-1 w-full
                                            {{ $isDone   ? 'bg-green-500'  : '' }}
                                            {{ $isOngoing ? 'bg-blue-500 animate-pulse' : '' }}
                                            {{ ! $isDone && ! $isOngoing ? 'bg-slate-200 dark:bg-slate-700' : '' }}
                                        "></div>

                                        {{-- Team 1 row --}}
                                        <div class="flex items-center justify-between gap-1 px-2 py-1.5
                                            {{ $team1IsWinner ? 'bg-green-50 dark:bg-green-900/20' : 'bg-white dark:bg-slate-900' }}">
                                            <span class="truncate text-xs
                                                {{ $team1IsWinner ? 'font-bold text-green-700 dark:text-green-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                {{ $team1Name }}
                                            </span>
                                            @if ($isDone || $isOngoing)
                                                <span class="shrink-0 text-xs font-bold tabular-nums
                                                    {{ $team1IsWinner ? 'text-green-600' : 'text-slate-500' }}">
                                                    {{ $match->score_team1 }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Divider --}}
                                        <div class="h-px bg-slate-100 dark:bg-slate-800"></div>

                                        {{-- Team 2 row --}}
                                        <div class="flex items-center justify-between gap-1 px-2 py-1.5
                                            {{ $team2IsWinner ? 'bg-green-50 dark:bg-green-900/20' : 'bg-white dark:bg-slate-900' }}">
                                            @if ($isBye)
                                                <span class="truncate text-xs italic text-slate-400">BYE</span>
                                            @else
                                                <span class="truncate text-xs
                                                    {{ $team2IsWinner ? 'font-bold text-green-700 dark:text-green-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                    {{ $team2Name }}
                                                </span>
                                                @if ($isDone || $isOngoing)
                                                    <span class="shrink-0 text-xs font-bold tabular-nums
                                                        {{ $team2IsWinner ? 'text-green-600' : 'text-slate-500' }}">
                                                        {{ $match->score_team2 }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </button>

                                </div>
                            @endforeach
                        </div>

                    </div>

                    {{-- Connector column between rounds --}}
                    @if (! $loop->last)
                        <div class="flex flex-col justify-around" style="min-width: 16px;">
                            @for ($i = 0; $i < ceil($matchCount / 2); $i++)
                                <div style="flex: 1; position: relative;">
                                    {{-- Vertical bar connecting two matches to one in next round --}}
                                    <div style="
                                        position: absolute;
                                        right: 0; left: 0;
                                        top: 25%; bottom: 25%;
                                        border-right: 1px solid #cbd5e1;
                                        border-top: 1px solid #cbd5e1;
                                        border-bottom: 1px solid #cbd5e1;
                                        border-radius: 0 4px 4px 0;
                                    " class="dark:border-slate-600">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    @endif

                @endforeach

            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400">
            <div class="flex items-center gap-1.5">
                <div class="h-3 w-3 rounded-full bg-slate-300"></div>
                <span>Scheduled</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="h-3 w-3 animate-pulse rounded-full bg-blue-500"></div>
                <span>Ongoing</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                <span>Done</span>
            </div>
        </div>
    @endif

    {{-- Match Detail Modal --}}
    @if ($showMatchModal && $this->selectedMatch)
        @php $m = $this->selectedMatch; @endphp
        <flux:modal wire:model.self="showMatchModal" class="md:w-[32rem]">
            <div class="space-y-5">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading size="lg">
                            @if ($m->team1_id === $m->team2_id)
                                {{ $m->team1->name }}
                                <flux:badge color="amber" class="ml-2">BYE</flux:badge>
                            @else
                                {{ $m->team1->name }} vs {{ $m->team2->name }}
                            @endif
                        </flux:heading>
                        @if ($m->round)
                            <flux:badge color="zinc" class="mt-1">{{ $m->round }}</flux:badge>
                        @endif
                    </div>
                    <x-status-badge :status="$m->status" size="lg" class="self-start shrink-0 mr-8" />
                </div>

                <flux:separator />

                {{-- Score --}}
                @if (in_array($m->status, ['ongoing', 'done']))
                    <div class="flex items-center justify-center gap-6 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 py-5 shadow-md shadow-indigo-500/20">
                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-200">{{ $m->team1->name }}</p>
                            <p class="text-4xl font-bold text-white tabular-nums">{{ $m->score_team1 }}</p>
                        </div>
                        <span class="text-2xl font-bold text-indigo-300">—</span>
                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-200">{{ $m->team2->name }}</p>
                            <p class="text-4xl font-bold text-white tabular-nums">{{ $m->score_team2 }}</p>
                        </div>
                    </div>

                    @if ($m->winner_id)
                        <div class="flex items-center justify-center gap-2 text-green-600 dark:text-green-400">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-semibold">Pemenang: {{ $m->winner->name }}</span>
                        </div>
                    @elseif ($m->status === 'done')
                        <p class="text-center text-sm text-slate-500">Hasil Seri</p>
                    @endif
                @endif

                {{-- Match details --}}
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3">
                    @if ($m->match_date)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal</dt>
                            <dd class="mt-0.5 text-sm text-slate-700 dark:text-slate-300">{{ $m->match_date->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                    @if ($m->venue)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Venue</dt>
                            <dd class="mt-0.5 text-sm text-slate-700 dark:text-slate-300">{{ $m->venue }}</dd>
                        </div>
                    @endif
                    @if ($m->referee)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Wasit</dt>
                            <dd class="mt-0.5 text-sm text-slate-700 dark:text-slate-300">{{ $m->referee }}</dd>
                        </div>
                    @endif
                </dl>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-1">
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost">Tutup</flux:button>
                    </flux:modal.close>
                    @if (! in_array($m->status, ['done', 'cancelled']))
                        <flux:button
                            href="{{ route('admin.matches.control', $m->id) }}"
                            wire:navigate
                            variant="primary"
                            icon="play"
                        >
                            Kelola
                        </flux:button>
                    @endif
                </div>

            </div>
        </flux:modal>
    @endif

</div>
