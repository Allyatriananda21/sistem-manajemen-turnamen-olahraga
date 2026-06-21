<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.matches') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            Daftar Pertandingan
        </a>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="truncate font-medium text-slate-700 dark:text-slate-300">
            {{ $match->team1->name }} vs {{ $match->team2->name }}
        </span>
    </div>

    {{-- Match Info Card --}}
    <flux:card class="space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-1">
                <flux:heading size="xl">
                    @if ($match->notes && str_contains($match->notes, 'BYE'))
                        {{ $match->team1->name }}
                        <flux:badge color="amber" class="ml-2">BYE</flux:badge>
                    @else
                        {{ $match->team1->name }}
                        <span class="mx-2 text-slate-400">vs</span>
                        {{ $match->team2->name }}
                    @endif
                </flux:heading>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($match->round)
                        <flux:badge color="zinc">{{ $match->round }}</flux:badge>
                    @endif
                    @if ($match->venue)
                        <flux:text class="text-sm text-slate-500">
                            <svg class="mr-1 inline h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $match->venue }}
                        </flux:text>
                    @endif
                    @if ($match->match_date)
                        <flux:text class="text-sm text-slate-500">
                            <svg class="mr-1 inline h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $match->match_date->format('d M Y, H:i') }}
                        </flux:text>
                    @endif
                    @if ($match->referee)
                        <flux:text class="text-sm text-slate-500">
                            <svg class="mr-1 inline h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $match->referee }}
                        </flux:text>
                    @endif
                </div>
            </div>

            {{-- Current status badge --}}
            <x-status-badge :status="$match->status" size="lg" class="self-start shrink-0" />
        </div>
    </flux:card>

    {{-- Status Progress Stepper --}}
    <flux:card class="space-y-5">
        <flux:heading size="lg">Alur Status Pertandingan</flux:heading>
        <flux:separator />

        <div class="flex items-center gap-0">
            @php
                $steps = [
                    'scheduled' => 'Scheduled',
                    'ongoing'   => 'Ongoing',
                    'done'      => 'Done',
                ];
                $order = array_keys($steps);
                $currentIndex = array_search($match->status, $order);
            @endphp

            @foreach ($steps as $stepStatus => $stepLabel)
                @php
                    $stepIndex = array_search($stepStatus, $order);
                    $isPast    = $currentIndex !== false && $stepIndex < $currentIndex;
                    $isCurrent = $match->status === $stepStatus;
                    $isFuture  = $currentIndex !== false && $stepIndex > $currentIndex;
                @endphp

                {{-- Step circle --}}
                <div class="flex flex-col items-center">
                    <div @class([
                        'flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold ring-2 transition-all',
                        'ring-green-500 bg-green-500 text-white'  => $isPast,
                        'ring-indigo-500 bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' => $isCurrent && $match->status !== 'cancelled',
                        'ring-slate-200 bg-slate-100 text-slate-400 dark:ring-slate-700 dark:bg-slate-800' => $isFuture,
                        'ring-red-300 bg-red-50 text-red-400 dark:ring-red-800 dark:bg-red-900/20' => $match->status === 'cancelled',
                    ])>
                        @if ($isPast)
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {{ $stepIndex + 1 }}
                        @endif
                    </div>
                    <span @class([
                        'mt-2 text-xs font-medium',
                        'text-green-600 dark:text-green-400'   => $isPast,
                        'text-indigo-600 dark:text-indigo-400' => $isCurrent && $match->status !== 'cancelled',
                        'text-slate-400'                       => $isFuture,
                        'text-red-400'                         => $match->status === 'cancelled',
                    ])>{{ $stepLabel }}</span>
                </div>

                {{-- Connector line (not after last step) --}}
                @if (! $loop->last)
                    <div @class([
                        'mb-5 h-0.5 flex-1 transition-all',
                        'bg-green-400' => $isPast,
                        'bg-slate-200 dark:bg-slate-700' => ! $isPast,
                    ])></div>
                @endif
            @endforeach

            {{-- Cancelled branch indicator --}}
            @if ($match->status === 'cancelled')
                <div class="ml-4 flex flex-col items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-white ring-2 ring-red-500 text-sm font-bold">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <span class="mt-2 text-xs font-medium text-red-500">Cancelled</span>
                </div>
            @endif
        </div>
    </flux:card>

    {{-- Action Buttons --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Ubah Status</flux:heading>
        <flux:separator />

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

            {{-- Advance button — disabled when no next step available --}}
            @if ($match->status === 'scheduled')
                <flux:button
                    wire:click="advance"
                    wire:confirm="Mulai pertandingan? Status akan berubah ke Ongoing."
                    variant="primary"
                    icon="play"
                    class="sm:flex-1"
                >
                    Mulai Pertandingan
                </flux:button>
            @elseif ($match->status === 'ongoing')
                <flux:button
                    wire:click="advance"
                    variant="primary"
                    icon="flag"
                    class="sm:flex-1"
                >
                    Selesaikan Pertandingan
                </flux:button>
            @elseif ($match->status === 'done')
                <div class="flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 dark:border-green-800 dark:bg-green-900/20 sm:flex-1">
                    <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <flux:text class="text-sm font-medium text-green-700 dark:text-green-400">
                            Pertandingan telah selesai.
                        </flux:text>
                        @if ($match->winner_id)
                            <flux:text class="text-xs text-green-600 dark:text-green-500">
                                Pemenang:
                                <span class="font-semibold">
                                    {{ $match->winner_id === $match->team1_id ? $match->team1->name : $match->team2->name }}
                                </span>
                            </flux:text>
                        @else
                            <flux:text class="text-xs text-green-600 dark:text-green-500">Hasil Seri</flux:text>
                        @endif
                    </div>
                </div>
            @elseif ($match->status === 'cancelled')
                <div class="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20 sm:flex-1">
                    <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <flux:text class="text-sm font-medium text-red-700 dark:text-red-400">
                        Pertandingan telah dibatalkan.
                    </flux:text>
                </div>
            @endif

            {{-- Cancel button — only when not done or already cancelled --}}
            @if (! in_array($match->status, ['done', 'cancelled']))
                <flux:button
                    wire:click="cancel"
                    wire:confirm="Batalkan pertandingan ini? Aksi ini tidak dapat diundo."
                    variant="danger"
                    icon="x-circle"
                >
                    Batalkan
                </flux:button>
            @endif

        </div>

        @if ($match->status === 'ongoing')
            {{-- Score display hint removed — input skor ada di section tersendiri di bawah --}}
        @endif
    </flux:card>

    {{-- Score Input — hanya aktif saat ongoing --}}
    @if ($match->status !== 'scheduled' && ! ($match->notes && str_contains($match->notes, 'BYE')))
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Input Skor</flux:heading>
                @if ($match->status !== 'ongoing')
                    <flux:badge color="zinc" size="sm">Read-only</flux:badge>
                @else
                    <flux:badge color="blue" size="sm">Sedang Berlangsung</flux:badge>
                @endif
            </div>
            <flux:separator />

            <div class="grid grid-cols-3 items-center gap-4">

                {{-- Team 1 Score --}}
                <div class="flex flex-col items-center gap-2">
                    <flux:text class="text-center text-sm font-semibold text-slate-600 dark:text-slate-400 truncate w-full text-center">
                        {{ $match->team1->name }}
                    </flux:text>
                    @if ($match->status === 'ongoing')
                        <flux:input
                            wire:model.live="scoreTeam1"
                            type="number"
                            min="0"
                            class="text-center text-3xl font-bold"
                            inputmode="numeric"
                        />
                        <flux:error name="scoreTeam1" />
                    @else
                        <div class="flex h-16 w-full items-center justify-center rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <span class="text-3xl font-bold text-slate-800 dark:text-slate-200">{{ $match->score_team1 }}</span>
                        </div>
                    @endif
                </div>

                {{-- VS separator with live preview --}}
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">vs</span>
                    @if ($match->status === 'ongoing')
                        <div class="rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 px-4 py-2 text-center shadow-md shadow-indigo-500/20">
                            <span class="text-xl font-bold text-white tabular-nums">
                                {{ $scoreTeam1 }} – {{ $scoreTeam2 }}
                            </span>
                        </div>
                        <span class="text-xs text-slate-400">preview</span>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 dark:border-slate-700 dark:bg-slate-800">
                            <span class="text-xl font-bold text-slate-700 dark:text-slate-300 tabular-nums">
                                {{ $match->score_team1 }} – {{ $match->score_team2 }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Team 2 Score --}}
                <div class="flex flex-col items-center gap-2">
                    <flux:text class="text-center text-sm font-semibold text-slate-600 dark:text-slate-400 truncate w-full text-center">
                        {{ $match->team2->name }}
                    </flux:text>
                    @if ($match->status === 'ongoing')
                        <flux:input
                            wire:model.live="scoreTeam2"
                            type="number"
                            min="0"
                            class="text-center text-3xl font-bold"
                            inputmode="numeric"
                        />
                        <flux:error name="scoreTeam2" />
                    @else
                        <div class="flex h-16 w-full items-center justify-center rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <span class="text-3xl font-bold text-slate-800 dark:text-slate-200">{{ $match->score_team2 }}</span>
                        </div>
                    @endif
                </div>

            </div>

            @if ($match->status === 'ongoing')
                <div class="flex justify-end">
                    <flux:button wire:click="saveScore" variant="primary" icon="check">
                        Simpan Skor
                    </flux:button>
                </div>
            @endif
        </flux:card>
    @endif

    {{-- Notes / Catatan Sanksi — aktif saat status bukan 'scheduled' --}}
    @if ($match->status !== 'scheduled')
        <flux:card class="space-y-5">
            <flux:heading size="lg">Catatan & Sanksi</flux:heading>
            <flux:separator />

            <flux:field>
                <flux:label>Catatan Wasit</flux:label>
                <flux:textarea
                    wire:model="notes"
                    placeholder="Isi catatan sanksi, kartu, denda, atau kejadian khusus..."
                    rows="4"
                    maxlength="2000"
                    :disabled="$match->status === 'cancelled'"
                />
                <flux:description>
                    @if ($match->status === 'cancelled')
                        Pertandingan dibatalkan — catatan tidak dapat diubah.
                    @else
                        Catatan ini juga akan menjadi dasar tagihan denda di modul POS.
                    @endif
                </flux:description>
                <flux:error name="notes" />
            </flux:field>

            @if ($match->status !== 'cancelled')
                <div class="flex justify-end">
                    <flux:button wire:click="saveNotes" variant="ghost" icon="document-text">
                        Simpan Catatan
                    </flux:button>
                </div>
            @endif
        </flux:card>
    @endif

    {{-- Back --}}
    <flux:button href="{{ route('admin.matches') }}" wire:navigate variant="ghost" icon="arrow-left">
        Kembali ke Daftar Pertandingan
    </flux:button>

    {{-- Zero Score Confirmation Modal --}}
    <flux:modal wire:model.self="showZeroScoreConfirm" class="md:w-[26rem]" :dismissible="false">
        <div class="space-y-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg">Skor masih 0 – 0</flux:heading>
                    <flux:text class="mt-1 text-slate-500">
                        Kedua tim belum ada yang mencetak gol. Apakah Anda yakin ingin menyelesaikan pertandingan dengan skor seri 0 – 0?
                    </flux:text>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="$set('showZeroScoreConfirm', false)"
                >
                    Kembali & Isi Skor
                </flux:button>
                <flux:button
                    wire:click="confirmFinishZeroScore"
                    variant="primary"
                    icon="flag"
                >
                    Ya, Selesaikan (0 – 0)
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
