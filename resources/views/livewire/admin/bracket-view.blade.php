<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Bracket Visual</flux:heading>
            <flux:text class="mt-1 text-slate-500">Alur pertandingan knockout per cabang olahraga.</flux:text>
        </div>

        {{-- Dropdown filter --}}
        @if ($this->availableSports->isNotEmpty())
            <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-2">
                <select wire:model.live="sportFilter"
                        class="w-full rounded-lg border border-[#c8e87d] bg-[#E4FD97] px-3 py-1.5 text-sm text-black focus:outline-none focus:ring-2 focus:ring-[#4a7c30] sm:w-56">
                    <option value="">Semua Cabang Olahraga</option>
                    @foreach ($this->availableSports as $sport)
                        <option value="{{ $sport }}">{{ $sport }}</option>
                    @endforeach
                </select>
            </flux:card>
        @endif
    </div>

    @if ($this->rounds->isEmpty())
        <flux:card class="secondary-card py-16 bg-[#E4FD97] border-[#c8e87d]">
            <div class="flex flex-col items-center gap-4 text-center">
                <svg class="h-12 w-12 text-[#4a7c30]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <flux:heading size="lg" class="text-[#4a7c30]">Belum ada bracket</flux:heading>
                <flux:text class="text-slate-400">Generate jadwal format Knockout terlebih dahulu.</flux:text>
                <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="primary" icon="bolt">Generator Jadwal</flux:button>
            </div>
        </flux:card>
    @else

        @php
            // Kelompokkan rounds berdasarkan sport_type tim1
            $allRounds = $this->rounds;
            $sportGroups = [];
            foreach ($allRounds as $rd) {
                $sport = $rd['matches']->first()?->team1?->sport_type ?? 'Lainnya';
                $sportGroups[$sport][] = $rd;
            }
            ksort($sportGroups);
            if ($sportFilter) {
                $sportGroups = array_filter($sportGroups, fn($k) => $k === $sportFilter, ARRAY_FILTER_USE_KEY);
            }
        @endphp

        @foreach ($sportGroups as $sportName => $rounds)

            {{-- ═══ SPORT SECTION ═══ --}}
            <div class="rounded-2xl border-2 overflow-hidden" style="border-color: rgba(228,253,151,0.2);">

                {{-- Sport header --}}
                <div class="flex items-center gap-3 px-6 py-4" style="background: #1e2b1d;">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: rgba(228,253,151,0.1); border: 1px solid rgba(228,253,151,0.2);">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#E4FD97;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold" style="color:#E4FD97; font-family:'Space Grotesk',sans-serif;">{{ $sportName }}</h2>
                        <p class="text-xs" style="color: rgba(228,253,151,0.4);">{{ count($rounds) }} babak · {{ collect($rounds)->sum(fn($r) => count($r['matches'])) }} pertandingan</p>
                    </div>
                </div>

                {{-- Rounds: vertical flow --}}
                <div class="space-y-0 p-6" style="background: rgba(30,43,29,0.5);">

                    @foreach ($rounds as $rIdx => $roundData)
                        @php
                            $roundLabel  = $roundData['round'];
                            $matches     = $roundData['matches'];
                            $isLastRound = $rIdx === array_key_last($rounds);
                            $isFinal     = strtolower($roundLabel) === 'final';
                        @endphp

                        {{-- ── BABAK HEADING ── --}}
                        <div class="flex items-center gap-3 {{ $rIdx > 0 ? 'mt-2' : '' }}">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-black"
                                 style="{{ $isFinal ? 'background:#E4FD97; color:#1e2b1d;' : 'background:rgba(228,253,151,0.15); color:#E4FD97;' }}">
                                {{ $rIdx + 1 }}
                            </div>
                            <span class="text-sm font-bold uppercase tracking-wider {{ $isFinal ? '' : 'text-slate-300' }}"
                                  style="{{ $isFinal ? 'color:#E4FD97;' : '' }}">
                                {{ $isFinal ? '🏆 ' : '' }}{{ $roundLabel }}
                            </span>
                            <div class="h-px flex-1" style="background: rgba(228,253,151,0.1);"></div>
                            <span class="text-xs text-slate-600">{{ count($matches) }} pertandingan</span>
                        </div>

                        {{-- ── MATCH CARDS ── --}}
                        <div class="mt-3 grid gap-3 {{ count($matches) > 1 ? 'sm:grid-cols-2' : 'sm:grid-cols-1 max-w-md' }}">
                            @foreach ($matches as $match)
                                @php
                                    $isBye     = $match->team1_id === $match->team2_id;
                                    $isDone    = $match->status === 'done';
                                    $isOngoing = $match->status === 'ongoing';
                                    $t1Win     = $isDone && $match->winner_id === $match->team1_id;
                                    $t2Win     = $isDone && $match->winner_id === $match->team2_id;

                                    $t1Logo = $match->team1->logo
                                        ? (str_starts_with($match->team1->logo, 'http://') || str_starts_with($match->team1->logo, 'https://')
                                            ? $match->team1->logo
                                            : (str_starts_with($match->team1->logo, 'public/')
                                                ? '/storage/' . str_replace('public/', '', $match->team1->logo)
                                                : '/storage/' . $match->team1->logo))
                                        : null;
                                    $t2Logo = (!$isBye && $match->team2?->logo)
                                        ? (str_starts_with($match->team2->logo, 'http://') || str_starts_with($match->team2->logo, 'https://')
                                            ? $match->team2->logo
                                            : (str_starts_with($match->team2->logo, 'public/')
                                                ? '/storage/' . str_replace('public/', '', $match->team2->logo)
                                                : '/storage/' . $match->team2->logo))
                                        : null;
                                @endphp

                                <button
                                    wire:click="openMatch({{ $match->id }})"
                                    wire:key="bm-{{ $match->id }}"
                                    class="w-full overflow-hidden rounded-2xl text-left transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl focus:outline-none"
                                    style="border: 1.5px solid {{ $isDone ? 'rgba(34,197,94,0.4)' : ($isOngoing ? 'rgba(59,130,246,0.5)' : 'rgba(228,253,151,0.12)') }};
                                           background: #1e2b1d;"
                                >
                                    {{-- Status stripe --}}
                                    <div class="h-1 w-full {{ $isDone ? 'bg-green-500' : ($isOngoing ? 'bg-blue-500 animate-pulse' : 'bg-slate-800') }}"></div>

                                    {{-- Status + date --}}
                                    <div class="flex items-center justify-between border-b px-4 py-2"
                                         style="border-color: rgba(228,253,151,0.07); background: rgba(0,0,0,0.15);">
                                        <span class="text-[10px] font-bold uppercase tracking-wider
                                            {{ $isDone ? 'text-green-500' : ($isOngoing ? 'text-blue-400' : 'text-slate-600') }}">
                                            {{ $isDone ? '✓ Selesai' : ($isOngoing ? '● Berlangsung' : '○ Terjadwal') }}
                                        </span>
                                        @if ($match->match_date)
                                            <span class="text-[10px] text-slate-600">{{ $match->match_date->format('d M Y') }}</span>
                                        @endif
                                    </div>

                                    {{-- Team 1 --}}
                                    <div class="flex items-center justify-between gap-3 px-4 py-3"
                                         style="{{ $t1Win ? 'background: rgba(34,197,94,0.08);' : '' }}">
                                        <div class="flex min-w-0 items-center gap-3">
                                            @if ($t1Logo)
                                                <img src="{{ $t1Logo }}" alt="{{ $match->team1->name }}"
                                                     class="h-8 w-8 shrink-0 rounded-lg object-cover shadow-md" />
                                            @else
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[10px] font-black text-white select-none"
                                                     style="background: linear-gradient(135deg,#4f46e5,#7c3aed);">
                                                    {{ strtoupper(substr($match->team1->name ?? '?', 0, 2)) }}
                                                </div>
                                            @endif
                                            <span class="truncate text-sm font-semibold {{ $t1Win ? 'text-green-400' : 'text-white' }}">
                                                {{ $match->team1->name ?? '—' }}
                                            </span>
                                            @if ($t1Win) <span class="shrink-0">🏆</span> @endif
                                        </div>
                                        @if ($isDone || $isOngoing)
                                            <span class="shrink-0 text-xl font-black tabular-nums {{ $t1Win ? 'text-green-400' : 'text-slate-400' }}">
                                                {{ $match->score_team1 }}
                                            </span>
                                        @else
                                            <span class="shrink-0 text-sm font-bold text-slate-700">—</span>
                                        @endif
                                    </div>

                                    {{-- VS divider --}}
                                    <div class="flex items-center gap-2 px-4"
                                         style="border-top: 1px solid rgba(228,253,151,0.06); border-bottom: 1px solid rgba(228,253,151,0.06);">
                                        <div class="h-px flex-1" style="background: rgba(228,253,151,0.06);"></div>
                                        <span class="py-1 text-[10px] font-black text-slate-700">VS</span>
                                        <div class="h-px flex-1" style="background: rgba(228,253,151,0.06);"></div>
                                    </div>

                                    {{-- Team 2 --}}
                                    @if ($isBye)
                                        <div class="px-4 py-3">
                                            <span class="text-sm italic text-slate-600">BYE — lolos otomatis</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between gap-3 px-4 py-3"
                                             style="{{ $t2Win ? 'background: rgba(34,197,94,0.08);' : '' }}">
                                            <div class="flex min-w-0 items-center gap-3">
                                                @if ($t2Logo)
                                                    <img src="{{ $t2Logo }}" alt="{{ $match->team2->name }}"
                                                         class="h-8 w-8 shrink-0 rounded-lg object-cover shadow-md" />
                                                @else
                                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[10px] font-black text-white select-none"
                                                         style="background: linear-gradient(135deg,#ec4899,#be185d);">
                                                        {{ strtoupper(substr($match->team2->name ?? '?', 0, 2)) }}
                                                    </div>
                                                @endif
                                                <span class="truncate text-sm font-semibold {{ $t2Win ? 'text-green-400' : 'text-white' }}">
                                                    {{ $match->team2->name ?? '—' }}
                                                </span>
                                                @if ($t2Win) <span class="shrink-0">🏆</span> @endif
                                            </div>
                                            @if ($isDone || $isOngoing)
                                                <span class="shrink-0 text-xl font-black tabular-nums {{ $t2Win ? 'text-green-400' : 'text-slate-400' }}">
                                                    {{ $match->score_team2 }}
                                                </span>
                                            @else
                                                <span class="shrink-0 text-sm font-bold text-slate-700">—</span>
                                            @endif
                                        </div>
                                    @endif

                                </button>
                            @endforeach
                        </div>

                        {{-- ── PANAH KE BABAK BERIKUTNYA ── --}}
                        @if (! $isLastRound)
                            <div class="flex flex-col items-center gap-1 py-3">
                                <div class="h-6 w-0.5" style="background: rgba(228,253,151,0.2);"></div>
                                <div class="flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                     style="background: rgba(74,124,48,0.15); color: #4a7c30; border: 1px solid rgba(74,124,48,0.3);">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    Pemenang lanjut ke {{ $rounds[$rIdx + 1]['round'] ?? 'babak berikutnya' }}
                                </div>
                                <div class="h-6 w-0.5" style="background: rgba(228,253,151,0.2);"></div>
                            </div>
                        @endif

                    @endforeach

                </div>
            </div>

        @endforeach

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-5 text-xs text-slate-500">
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-700"></span>Terjadwal</span>
            <span class="flex items-center gap-1.5 text-blue-400"><span class="h-2.5 w-2.5 animate-pulse rounded-full bg-blue-500"></span>Berlangsung</span>
            <span class="flex items-center gap-1.5 text-green-500"><span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>Selesai · 🏆 Pemenang</span>
            <span class="flex items-center gap-1.5 text-[#4a7c30]">Klik kartu untuk detail</span>
        </div>

    @endif

    {{-- Match Detail Modal --}}
    @if ($showMatchModal && $this->selectedMatch)
        @php $m = $this->selectedMatch; @endphp
        <flux:modal wire:model.self="showMatchModal" class="md:w-[32rem]">
            <div class="space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading size="lg">
                            @if ($m->team1_id === $m->team2_id)
                                {{ $m->team1->name }} <flux:badge color="amber" class="ml-2">BYE</flux:badge>
                            @else
                                {{ $m->team1->name }} vs {{ $m->team2->name }}
                            @endif
                        </flux:heading>
                        <div class="mt-1.5 flex flex-wrap gap-1">
                            @if ($m->round) <flux:badge color="zinc">{{ $m->round }}</flux:badge> @endif
                            @if ($m->team1?->sport_type) <flux:badge color="green">{{ $m->team1->sport_type }}</flux:badge> @endif
                        </div>
                    </div>
                    <x-status-badge :status="$m->status" size="lg" class="self-start shrink-0 mr-8"/>
                </div>

                <flux:separator/>

                @if (in_array($m->status, ['ongoing', 'done']))
                    <div class="flex items-center justify-center gap-6 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 py-5 shadow-md">
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
                        <div class="flex items-center justify-center gap-2 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3">
                            <span class="text-lg">🏆</span>
                            <span class="text-sm font-bold text-green-400">Pemenang: {{ $m->winner->name }}</span>
                        </div>
                    @elseif ($m->status === 'done')
                        <p class="text-center text-sm text-slate-500">Hasil Seri</p>
                    @endif
                @else
                    <div class="flex items-center justify-center gap-6 rounded-xl border border-slate-700 bg-slate-800/30 py-6">
                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $m->team1->name }}</p>
                            <p class="text-3xl font-bold text-slate-600">—</p>
                        </div>
                        <span class="text-xl font-bold text-slate-700">VS</span>
                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $m->team2->name }}</p>
                            <p class="text-3xl font-bold text-slate-600">—</p>
                        </div>
                    </div>
                @endif

                <dl class="grid grid-cols-2 gap-x-4 gap-y-3">
                    @if ($m->match_date)
                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal</dt><dd class="mt-0.5 text-sm text-slate-300">{{ $m->match_date->format('d M Y, H:i') }}</dd></div>
                    @endif
                    @if ($m->venue)
                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Venue</dt><dd class="mt-0.5 text-sm text-slate-300">{{ $m->venue }}</dd></div>
                    @endif
                    @if ($m->referee)
                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Wasit</dt><dd class="mt-0.5 text-sm text-slate-300">{{ $m->referee }}</dd></div>
                    @endif
                </dl>

                <div class="flex justify-end gap-3 pt-1">
                    <flux:modal.close><flux:button type="button" variant="ghost">Tutup</flux:button></flux:modal.close>
                    @if (! in_array($m->status, ['done', 'cancelled']))
                        <flux:button href="{{ route('admin.matches.control', $m->id) }}" wire:navigate variant="primary" icon="play">
                            Kelola Pertandingan
                        </flux:button>
                    @endif
                </div>
            </div>
        </flux:modal>
    @endif

</div>
