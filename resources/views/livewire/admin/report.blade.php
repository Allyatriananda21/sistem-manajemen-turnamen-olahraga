<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl">Laporan Keuangan</flux:heading>
        <flux:text class="mt-1 text-slate-500">Ringkasan total pendapatan turnamen dari seluruh transaksi POS.</flux:text>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Registrasi --}}
        <flux:card class="flex flex-col gap-2">
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
        </flux:card>

        {{-- Retail/Kantin --}}
        <flux:card class="flex flex-col gap-2">
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
        </flux:card>

        {{-- Denda --}}
        <flux:card class="flex flex-col gap-2">
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
        </flux:card>

        {{-- Total Keseluruhan --}}
        <flux:card class="flex flex-col gap-2 border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-950/30">
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
        </flux:card>

    </div>

    {{-- Recent Transactions Table --}}
    <flux:card class="overflow-hidden p-0">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
            <flux:heading size="lg">10 Transaksi Terbaru</flux:heading>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Tipe</flux:table.column>
                <flux:table.column>Tim</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Metode</flux:table.column>
                <flux:table.column>Kasir</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($recentTransactions as $trx)
                    <flux:table.row wire:key="trx-{{ $trx->id }}">
                        <flux:table.cell>
                            <flux:text class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $trx->created_at->format('d M Y, H:i') }}
                            </flux:text>
                        </flux:table.cell>

                        <flux:table.cell>
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
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:text class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $trx->team?->name ?? '—' }}
                            </flux:text>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm font-semibold text-slate-900 dark:text-white">
                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="{{ $trx->payment_method === 'qris' ? 'violet' : 'zinc' }}" size="sm">
                                {{ strtoupper($trx->payment_method) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:text class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $trx->cashier_name ?? '—' }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-12 text-center">
                            <flux:text class="text-slate-400">Belum ada transaksi.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

</div>
