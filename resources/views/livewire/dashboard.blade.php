<div class="space-y-6">

    {{-- Filter Panel --}}
    <flux:card class="filter-card space-y-4 bg-[#E4FD97] border-[#c8e87d]">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-[#3a5a2a] dark:text-[#E4FD97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                <flux:heading size="sm" class="text-[#1e2b1d] dark:text-[#E4FD97]">Filter Dashboard</flux:heading>
            </div>

            @if ($filterDateFrom || $filterDateTo || $filterRound || $filterTeamId !== '')
                <flux:button wire:click="resetFilters" size="sm" variant="ghost" icon="x-mark">
                    Reset Filter
                </flux:button>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Date From --}}
            <flux:field>
                <flux:label>Dari Tanggal</flux:label>
                <flux:input
                    wire:model.live="filterDateFrom"
                    type="date"
                    :max="$filterDateTo ?: null"
                />
            </flux:field>

            {{-- Date To --}}
            <flux:field>
                <flux:label>Sampai Tanggal</flux:label>
                <flux:input
                    wire:model.live="filterDateTo"
                    type="date"
                    :min="$filterDateFrom ?: null"
                />
            </flux:field>

            {{-- Round --}}
            <flux:field>
                <flux:label>Babak / Round</flux:label>
                <select wire:model.live="filterRound" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Babak</option>
                    @foreach ($this->allRounds as $round)
                        <option value="{{ $round }}">{{ $round }}</option>
                    @endforeach
                </select>
            </flux:field>

            {{-- Competitor --}}
            <flux:field>
                <flux:label>Tim / Competitor</flux:label>
                <select wire:model.live="filterTeamId" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tim</option>
                    @foreach ($this->allApprovedTeams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </flux:field>

        </div>

        {{-- Active filter badges --}}
        @if ($filterDateFrom || $filterDateTo || $filterRound || $filterTeamId !== '')
            <div class="flex flex-wrap gap-2 pt-1">
                @if ($filterDateFrom)
                    <flux:badge color="blue" size="sm">Dari: {{ $filterDateFrom }}</flux:badge>
                @endif
                @if ($filterDateTo)
                    <flux:badge color="blue" size="sm">Sampai: {{ $filterDateTo }}</flux:badge>
                @endif
                @if ($filterRound)
                    <flux:badge color="indigo" size="sm">Babak: {{ $filterRound }}</flux:badge>
                @endif
                @if ($filterTeamId !== '')
                    @php $selectedTeam = $this->allApprovedTeams->firstWhere('id', $filterTeamId); @endphp
                    @if ($selectedTeam)
                        <flux:badge color="purple" size="sm">Tim: {{ $selectedTeam->name }}</flux:badge>
                    @endif
                @endif
            </div>
        @endif
    </flux:card>

    {{-- Competition Overview Widget --}}
    <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-white shadow-md shadow-amber-500/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m12 3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg">Competition Overview</flux:heading>
                    <flux:text class="text-xs text-slate-400">Ringkasan status turnamen saat ini</flux:text>
                </div>
            </div>

            {{-- Competition status badge --}}
            @php
                $statusConfig = match($this->competitionStatus) {
                    'In-Play'     => ['color' => 'blue',  'dot' => 'bg-blue-500',  'label' => 'In-Play'],
                    'Selesai'     => ['color' => 'green', 'dot' => 'bg-green-500', 'label' => 'Selesai'],
                    default       => ['color' => 'zinc',  'dot' => 'bg-slate-400', 'label' => 'Belum Mulai'],
                };
            @endphp
            <div class="flex items-center gap-2 rounded-full border px-3 py-1.5
                {{ $this->competitionStatus === 'In-Play'
                    ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20'
                    : ($this->competitionStatus === 'Selesai'
                        ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20'
                        : 'border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800') }}">
                {{-- Animated pulse dot for In-Play --}}
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    @if ($this->competitionStatus === 'In-Play')
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                    @endif
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                </span>
                <span class="text-xs font-semibold
                    {{ $this->competitionStatus === 'In-Play'
                        ? 'text-blue-700 dark:text-blue-300'
                        : ($this->competitionStatus === 'Selesai'
                            ? 'text-green-700 dark:text-green-300'
                            : 'text-slate-500 dark:text-slate-400') }}">
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>

        <flux:separator />

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">

            {{-- Total tim approved --}}
            <div class="flex flex-col gap-1 rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-800 dark:bg-indigo-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Tim Approved</p>
                <p class="text-3xl font-bold text-indigo-700 dark:text-indigo-300">
                    {{ $this->totalApprovedTeams }}
                </p>
                <flux:text class="text-xs text-indigo-500/70 dark:text-indigo-400/70">tim peserta</flux:text>
            </div>

            {{-- Jumlah babak --}}
            <div class="flex flex-col gap-1 rounded-xl border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-400">Total Babak</p>
                <p class="text-3xl font-bold text-purple-700 dark:text-purple-300">
                    {{ $this->uniqueRounds->count() }}
                </p>
                <flux:text class="text-xs text-purple-500/70 dark:text-purple-400/70">babak unik</flux:text>
            </div>

            {{-- Rounds list --}}
            <div class="col-span-2 flex flex-col gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50 sm:col-span-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Babak</p>
                @if ($this->uniqueRounds->isEmpty())
                    <flux:text class="text-sm text-slate-400">—</flux:text>
                @else
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($this->uniqueRounds as $round)
                            <flux:badge color="zinc" size="sm">{{ $round }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        @if ($this->totalApprovedTeams === 0)
            <div class="flex items-center justify-center gap-3 rounded-xl border border-dashed border-slate-200 py-4 dark:border-slate-700">
                <flux:text class="text-sm text-slate-400">Belum ada tim yang disetujui.</flux:text>
                <flux:button href="{{ route('admin.teams') }}" wire:navigate variant="ghost" size="sm" icon="user-group">
                    Kelola Tim
                </flux:button>
            </div>
        @endif

    </flux:card>

    {{-- Revenue Chart --}}
    <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">

        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-green-500 to-teal-600 text-white shadow-md shadow-green-500/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <flux:heading size="lg">Grafik Pendapatan</flux:heading>
                <flux:text class="text-xs text-slate-400">Total transaksi per hari per tipe</flux:text>
            </div>
        </div>

        <flux:separator />

        @if (empty($this->revenueChartData['labels']))
            <div class="flex flex-col items-center gap-2 py-10 text-center">
                <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <flux:text class="text-slate-400">Belum ada data transaksi.</flux:text>
            </div>
        @else
            {{-- wire:ignore prevents Livewire DOM diffing from destroying the canvas --}}
            <div wire:ignore>
                <canvas id="revenueChart" style="max-height: 320px;"></canvas>
            </div>
        @endif

    </flux:card>

    {{-- Participation Statistics Widget --}}
    <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">

        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-violet-500 to-pink-500 text-white shadow-md shadow-violet-500/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <flux:heading size="lg">Statistik Partisipasi</flux:heading>
                <flux:text class="text-xs text-slate-400">Tim dan pertandingan dalam turnamen</flux:text>
            </div>
        </div>

        <flux:separator />

        @php
            $breakdown  = $this->matchStatusBreakdown;
            $matchDone  = $breakdown['done'];
            $matchLeft  = $breakdown['scheduled'] + $breakdown['ongoing'];
            $donePercent = $this->totalMatches > 0
                ? round(($matchDone / $this->totalMatches) * 100)
                : 0;
            $approvedPercent = $this->totalTeams > 0
                ? round(($this->totalApprovedTeams / $this->totalTeams) * 100)
                : 0;
        @endphp

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">

            {{-- Total tim terdaftar --}}
            <div class="flex flex-col gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Tim</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $this->totalTeams }}</p>
                <flux:text class="text-xs text-slate-500">terdaftar</flux:text>
            </div>

            {{-- Tim approved --}}
            <div class="flex flex-col gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-800 dark:bg-indigo-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Tim Approved</p>
                <p class="text-3xl font-bold text-indigo-700 dark:text-indigo-300">{{ $this->totalApprovedTeams }}</p>
                <div class="mt-1">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-indigo-200 dark:bg-indigo-900">
                        <div
                            class="h-full rounded-full bg-indigo-500 transition-all"
                            style="width: {{ $approvedPercent }}%"
                        ></div>
                    </div>
                    <flux:text class="mt-1 text-xs text-indigo-500/70 dark:text-indigo-400/70">
                        {{ $approvedPercent }}% dari total
                    </flux:text>
                </div>
            </div>

            {{-- Match selesai --}}
            <div class="flex flex-col gap-1.5 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-400">Match Selesai</p>
                <p class="text-3xl font-bold text-green-700 dark:text-green-300">{{ $matchDone }}</p>
                <div class="mt-1">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-green-200 dark:bg-green-900">
                        <div
                            class="h-full rounded-full bg-green-500 transition-all"
                            style="width: {{ $donePercent }}%"
                        ></div>
                    </div>
                    <flux:text class="mt-1 text-xs text-green-500/70 dark:text-green-400/70">
                        {{ $donePercent }}% dari total
                    </flux:text>
                </div>
            </div>

            {{-- Match tersisa --}}
            <div class="flex flex-col gap-1.5 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-400">Match Tersisa</p>
                <p class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $matchLeft }}</p>
                <flux:text class="text-xs text-amber-500/70 dark:text-amber-400/70">
                    scheduled + ongoing
                </flux:text>
            </div>

        </div>

    </flux:card>

    {{-- Fixtures Summary Card --}}
    <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">

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

@push('scripts')
<script>
(function () {
    function loadChartJs(callback) {
        if (window.Chart) { callback(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js';
        s.onload = callback;
        document.head.appendChild(s);
    }

    const chartData = @json($this->revenueChartData);

    if (! chartData.labels || chartData.labels.length === 0) {
        return;
    }

    function formatRupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    function initChart() {
        loadChartJs(function () {
            const canvas = document.getElementById('revenueChart');
            if (! canvas) return;

            if (canvas._chartInstance) {
                canvas._chartInstance.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor   = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            const labelColor  = isDark ? '#94a3b8' : '#64748b';

            const chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Registrasi',
                            data: chartData.datasets.registrasi,
                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            borderColor:     'rgba(99, 102, 241, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Retail',
                            data: chartData.datasets.retail,
                            backgroundColor: 'rgba(20, 184, 166, 0.7)',
                            borderColor:     'rgba(20, 184, 166, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Denda',
                            data: chartData.datasets.denda,
                            backgroundColor: 'rgba(245, 158, 11, 0.7)',
                            borderColor:     'rgba(245, 158, 11, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            labels: { color: labelColor, font: { size: 12 } },
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + formatRupiah(ctx.parsed.y);
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            stacked: false,
                            grid: { color: gridColor },
                            ticks: { color: labelColor, font: { size: 11 } },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: {
                                color: labelColor,
                                font: { size: 11 },
                                callback: function (value) { return formatRupiah(value); },
                            },
                        },
                    },
                },
            });

            canvas._chartInstance = chart;
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }

    document.addEventListener('livewire:navigated', initChart);
})();
</script>
@endpush