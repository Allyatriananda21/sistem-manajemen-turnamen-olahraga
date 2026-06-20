<div class="space-y-6">

    {{-- Fixtures Summary Card --}}
    <flux:card class="space-y-5">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg">Fixtures Summary</flux:heading>
                    <flux:text class="text-xs text-slate-400">Ringkasan jadwal pertandingan</flux:text>
                </div>
            </div>

            {{-- Jadwal status badge --}}
            @if ($this->totalMatches > 0)
                <div class="flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 dark:bg-green-900/20">
                    <svg class="h-3.5 w-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-semibold text-green-700 dark:text-green-400">Jadwal sudah final</span>
                </div>
            @else
                <div class="flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 dark:bg-slate-800">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Belum ada jadwal</span>
                </div>
            @endif
        </div>

        <flux:separator />

        {{-- Total + Breakdown --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">

            {{-- Total --}}
            <div class="col-span-2 flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50 sm:col-span-1">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-white">{{ $this->totalMatches }}</p>
                    <p class="mt-0.5 text-xs text-slate-400">pertandingan</p>
                </div>
            </div>

            {{-- Scheduled --}}
            <div class="flex flex-col gap-1 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Scheduled</p>
                <p class="text-2xl font-bold text-zinc-700 dark:text-zinc-300">
                    {{ $this->matchStatusBreakdown['scheduled'] }}
                </p>
                <flux:badge color="zinc" size="sm" class="self-start">Terjadwal</flux:badge>
            </div>

            {{-- Ongoing --}}
            <div class="flex flex-col gap-1 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-400">Ongoing</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    {{ $this->matchStatusBreakdown['ongoing'] }}
                </p>
                <flux:badge color="blue" size="sm" class="self-start">Berjalan</flux:badge>
            </div>

            {{-- Done --}}
            <div class="flex flex-col gap-1 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-400">Done</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                    {{ $this->matchStatusBreakdown['done'] }}
                </p>
                <flux:badge color="green" size="sm" class="self-start">Selesai</flux:badge>
            </div>

            {{-- Cancelled --}}
            <div class="flex flex-col gap-1 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-400">Cancelled</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                    {{ $this->matchStatusBreakdown['cancelled'] }}
                </p>
                <flux:badge color="red" size="sm" class="self-start">Dibatalkan</flux:badge>
            </div>

        </div>

        {{-- Empty state hint --}}
        @if ($this->totalMatches === 0)
            <div class="flex items-center justify-center gap-3 rounded-xl border border-dashed border-slate-200 py-6 dark:border-slate-700">
                <flux:text class="text-sm text-slate-400">Belum ada pertandingan. Generate jadwal terlebih dahulu.</flux:text>
                <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="ghost" size="sm" icon="bolt">
                    Generator Jadwal
                </flux:button>
            </div>
        @endif

    </flux:card>

</div>
