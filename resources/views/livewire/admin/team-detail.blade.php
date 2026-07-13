<div class="space-y-6">

    {{-- Breadcrumb + Back --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.teams') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            Daftar Tim
        </a>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-700 dark:text-slate-300 font-medium truncate">{{ $team->name }}</span>
    </div>

    {{-- Header Card --}}
    <flux:card class="flex flex-col gap-6 sm:flex-row sm:items-start">

        {{-- Logo --}}
        <div class="shrink-0">
            @if ($team->logo)
                @php
                    $logoUrl = str_starts_with($team->logo, 'http://') || str_starts_with($team->logo, 'https://')
                        ? $team->logo
                        : (str_starts_with($team->logo, 'public/')
                            ? '/storage/' . str_replace('public/', '', $team->logo)
                            : '/storage/' . $team->logo);
                @endphp
                <img
                    src="{{ $logoUrl }}"
                    alt="Logo {{ $team->name }}"
                    class="h-24 w-24 rounded-2xl object-cover shadow-md border border-slate-200 dark:border-slate-700"
                />
            @else
                <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-3xl font-bold shadow-md select-none">
                    {{ strtoupper(substr($team->name, 0, 2)) }}
                </div>
            @endif
        </div>

        {{-- Identity --}}
        <div class="flex-1 space-y-2">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl">{{ $team->name }}</flux:heading>

                <x-status-badge :status="$team->status" type="team" size="lg" />

                @if ($team->payment_status === 'paid')
                    <flux:badge color="green" size="lg">PAID</flux:badge>
                @else
                    <flux:badge color="red" size="lg">UNPAID</flux:badge>
                @endif
            </div>

            <flux:text class="text-slate-500">
                {{ $team->sport_type }}
                @if ($team->invoice_number)
                    &nbsp;·&nbsp;
                    <span class="font-mono text-xs text-slate-400">{{ $team->invoice_number }}</span>
                @endif
            </flux:text>

            <flux:text class="text-xs text-slate-400">
                Terdaftar {{ $team->registered_at->format('d M Y, H:i') }}
            </flux:text>
        </div>

    </flux:card>

    {{-- Detail Grid --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Info Tim --}}
        <flux:card class="space-y-5">
            <flux:heading size="lg">Informasi Tim</flux:heading>
            <flux:separator />

            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Tim</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">{{ $team->name }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Cabang Olahraga</dt>
                    <dd class="mt-1">
                        <flux:badge color="zinc">{{ $team->sport_type }}</flux:badge>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Pelatih</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">
                        {{ $team->coach_name ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Pendaftaran</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">
                        {{ $team->registered_at->format('d M Y, H:i') }}
                    </dd>
                </div>
            </dl>
        </flux:card>

        {{-- Info Kontak & Pembayaran --}}
        <flux:card class="space-y-5">
            <flux:heading size="lg">Kontak & Pembayaran</flux:heading>
            <flux:separator />

            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Contact Person</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">{{ $team->contact_person }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Telepon</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">{{ $team->phone }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Pembayaran</dt>
                    <dd class="mt-1">
                        @if ($team->payment_status === 'paid')
                            <div class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm font-semibold">Lunas</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-red-500 dark:text-red-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-sm font-semibold">Belum Lunas</span>
                            </div>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Invoice</dt>
                    <dd class="mt-1 text-sm font-mono text-slate-800 dark:text-slate-200">
                        {{ $team->invoice_number ?? '—' }}
                    </dd>
                </div>
            </dl>
        </flux:card>

    </div>

    {{-- Back Button --}}
    <div>
        <flux:button href="{{ route('admin.teams') }}" wire:navigate variant="ghost" icon="arrow-left">
            Kembali ke Daftar Tim
        </flux:button>
    </div>

</div>
