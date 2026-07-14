<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Laporan Keuangan</flux:heading>
        <flux:text class="mt-1 text-slate-500">Ringkasan total pendapatan turnamen dari seluruh transaksi POS.</flux:text>
    </div>

    {{-- Summary Cards (klik untuk filter transaksi) --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Registrasi --}}
        <button
            wire:click="filterByTipe('registrasi')"
            class="text-left w-full transition-transform duration-150 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-xl"
        >
            <flux:card class="secondary-card flex flex-col gap-2 h-full bg-[#E4FD97] border-[#c8e87d]
                {{ $filterTipe === 'registrasi' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <flux:text class="text-sm font-medium text-slate-500">Registrasi Tim</flux:text>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    Rp {{ number_format($totalRegistrasi, 0, ',', '.') }}
                </p>
                @if ($filterTipe === 'registrasi')
                    <flux:badge color="blue" size="sm" class="w-fit">Filter Aktif</flux:badge>
                @else
                    <flux:text class="text-xs text-slate-400">Klik untuk filter</flux:text>
                @endif
            </flux:card>
        </button>

        {{-- Retail/Kantin --}}
        <button
            wire:click="filterByTipe('retail')"
            class="text-left w-full transition-transform duration-150 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-400 rounded-xl"
        >
            <flux:card class="secondary-card flex flex-col gap-2 h-full bg-[#E4FD97] border-[#c8e87d]
                {{ $filterTipe === 'retail' ? 'ring-2 ring-green-500' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <flux:text class="text-sm font-medium text-slate-500">Retail / Kantin</flux:text>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    Rp {{ number_format($totalRetail, 0, ',', '.') }}
                </p>
                @if ($filterTipe === 'retail')
                    <flux:badge color="green" size="sm" class="w-fit">Filter Aktif</flux:badge>
                @else
                    <flux:text class="text-xs text-slate-400">Klik untuk filter</flux:text>
                @endif
            </flux:card>
        </button>

        {{-- Denda --}}
        <button
            wire:click="filterByTipe('denda')"
            class="text-left w-full transition-transform duration-150 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-red-400 rounded-xl"
        >
            <flux:card class="secondary-card flex flex-col gap-2 h-full bg-[#E4FD97] border-[#c8e87d]
                {{ $filterTipe === 'denda' ? 'ring-2 ring-red-500' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <flux:text class="text-sm font-medium text-slate-500">Denda</flux:text>
                </div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    Rp {{ number_format($totalDenda, 0, ',', '.') }}
                </p>
                @if ($filterTipe === 'denda')
                    <flux:badge color="red" size="sm" class="w-fit">Filter Aktif</flux:badge>
                @else
                    <flux:text class="text-xs text-slate-400">Klik untuk filter</flux:text>
                @endif
            </flux:card>
        </button>

        {{-- Total Keseluruhan --}}
        <button
            wire:click="resetFilter"
            class="text-left w-full transition-transform duration-150 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-400 rounded-xl"
        >
            <flux:card class="secondary-card flex flex-col gap-2 h-full border-indigo-200 bg-[#E4FD97] dark:border-indigo-800 dark:bg-indigo-950/30
                {{ is_null($filterTipe) ? 'ring-2 ring-indigo-500' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/50">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <flux:text class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Total Keseluruhan</flux:text>
                </div>
                <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">
                    Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                </p>
                @if (is_null($filterTipe))
                    <flux:badge color="indigo" size="sm" class="w-fit">Semua Tipe</flux:badge>
                @else
                    <flux:text class="text-xs text-slate-400">Klik untuk tampilkan semua</flux:text>
                @endif
            </flux:card>
        </button>

    </div>

    {{-- Grafik Pendapatan --}}
    <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">

        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-green-500 to-teal-600 text-white shadow-md shadow-green-500/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg">Grafik Pendapatan</flux:heading>
                    <flux:text class="text-xs text-slate-400">
                        @if ($filterTipe)
                            Menampilkan transaksi: <span class="font-semibold capitalize">{{ $filterTipe }}</span>
                        @else
                            Total transaksi per hari per tipe
                        @endif
                    </flux:text>
                </div>
            </div>

            @if ($filterTipe)
                <flux:button wire:click="resetFilter" size="sm" variant="ghost" icon="x-mark">
                    Reset Filter
                </flux:button>
            @endif
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
            <div wire:ignore>
                <canvas id="reportRevenueChart" style="max-height: 320px;"></canvas>
            </div>
        @endif

    </flux:card>

    {{-- Tabel Transaksi --}}
    <flux:card class="secondary-card overflow-hidden p-0 bg-[#E4FD97] border-[#c8e87d]">
        <div class="flex items-center justify-between border-b border-[#c8e87d] px-6 py-4 gap-3">
            <div>
                <flux:heading size="lg" class="text-[#1e2b1d] dark:text-[#E4FD97]">
                    @if ($filterTipe === 'denda')
                        Transaksi Denda
                    @elseif ($filterTipe === 'registrasi')
                        Transaksi Registrasi
                    @elseif ($filterTipe === 'retail')
                        Transaksi Retail
                    @else
                        Semua Transaksi
                    @endif
                </flux:heading>
                <flux:text class="text-xs text-slate-400 mt-0.5">
                    {{ $this->transactions->count() }} transaksi
                    @if ($filterTipe)
                        · tipe: <span class="font-semibold capitalize">{{ $filterTipe }}</span>
                    @endif
                </flux:text>
            </div>
            @if ($filterTipe)
                <flux:button wire:click="resetFilter" size="sm" variant="ghost" icon="x-mark">
                    Tampilkan Semua
                </flux:button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-[#c8e87d] bg-[#d8f57a]/40 dark:bg-[#2a3d1a]">
                        <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Tanggal</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Tipe</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Tim</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Total</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Metode</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Kasir</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-[#4a7c30]/70 dark:text-[#E4FD97]/50">Keterangan</th>                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c8e87d]/50 dark:divide-[rgba(228,253,151,0.08)]">
                    @forelse ($this->transactions as $trx)
                        <tr wire:key="trx-{{ $trx->id }}"
                            class="transition-colors duration-100 hover:bg-[#d8f57a]/30 dark:hover:bg-white/5
                                {{ $loop->even ? 'bg-[#E4FD97]/60 dark:bg-[#2a3d1a]/60' : 'bg-[#E4FD97] dark:bg-[#2a3d1a]' }}">

                            <td class="whitespace-nowrap px-6 py-3 text-sm text-[#1e2b1d]/70 dark:text-slate-400">
                                {{ $trx->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $typeColors = [
                                        'registrasi' => 'blue',
                                        'retail'     => 'green',
                                        'denda'      => 'red',
                                    ];
                                    $typeLabels = [
                                        'registrasi' => 'Registrasi',
                                        'retail'     => 'Retail',
                                        'denda'      => 'Denda',
                                    ];
                                @endphp
                                <flux:badge color="{{ $typeColors[$trx->transaction_type] ?? 'zinc' }}" size="sm">
                                    {{ $typeLabels[$trx->transaction_type] ?? $trx->transaction_type }}
                                </flux:badge>
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-[#1e2b1d] dark:text-slate-200">
                                {{ $trx->team?->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="text-sm font-bold text-[#1e2b1d] dark:text-white">
                                    Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <flux:badge color="{{ $trx->payment_method === 'qris' ? 'violet' : 'zinc' }}" size="sm">
                                    {{ strtoupper($trx->payment_method) }}
                                </flux:badge>
                            </td>

                            <td class="px-4 py-3 text-sm text-[#4a7c30] dark:text-[#E4FD97]/70">
                                {{ $trx->cashier_name ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-[#1e2b1d]/60 dark:text-slate-400 max-w-[200px]">
                                @if ($trx->notes)
                                    <span title="{{ $trx->notes }}" class="line-clamp-2">{{ $trx->notes }}</span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-14 text-center text-sm text-[#4a7c30]/50 dark:text-[#E4FD97]/30">
                                Belum ada transaksi
                                @if ($filterTipe)
                                    dengan tipe <span class="font-semibold capitalize">{{ $filterTipe }}</span>.
                                @else
                                    .
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
    const filterTipe = @json($filterTipe);

    if (! chartData.labels || chartData.labels.length === 0) {
        return;
    }

    function formatRupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    function buildDatasets(data, filter) {
        const all = [
            { label: 'Registrasi', key: 'registrasi', bg: 'rgba(99, 102, 241, 0.7)',  border: 'rgba(99, 102, 241, 1)' },
            { label: 'Retail',     key: 'retail',     bg: 'rgba(20, 184, 166, 0.7)',  border: 'rgba(20, 184, 166, 1)' },
            { label: 'Denda',      key: 'denda',      bg: 'rgba(245, 158, 11, 0.7)',  border: 'rgba(245, 158, 11, 1)' },
        ];
        const active = filter ? all.filter(d => d.key === filter) : all;
        return active.map(d => ({
            label: d.label,
            data: data.datasets[d.key] || [],
            backgroundColor: d.bg,
            borderColor: d.border,
            borderWidth: 1,
            borderRadius: 4,
        }));
    }

    function initChart() {
        loadChartJs(function () {
            const canvas = document.getElementById('reportRevenueChart');
            if (! canvas) return;

            if (canvas._chartInstance) {
                canvas._chartInstance.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            const labelColor = isDark ? '#94a3b8' : '#64748b';

            const chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: buildDatasets(chartData, filterTipe),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { color: labelColor, font: { size: 12 } } },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + formatRupiah(ctx.parsed.y);
                                },
                            },
                        },
                    },
                    scales: {
                        x: { stacked: false, grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } } },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: labelColor, font: { size: 11 }, callback: function (v) { return formatRupiah(v); } },
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
