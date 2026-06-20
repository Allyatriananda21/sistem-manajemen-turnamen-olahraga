<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl">Generator Jadwal</flux:heading>
        <flux:text class="mt-1 text-slate-500">Buat jadwal pertandingan otomatis dari tim yang sudah disetujui.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Left: Form Generator --}}
        <div class="space-y-5 lg:col-span-1">
            <flux:card class="space-y-5">
                <flux:heading size="lg">Pengaturan</flux:heading>
                <flux:separator />

                {{-- Format dropdown --}}
                <flux:field>
                    <flux:label>Format Kompetisi</flux:label>
                    <flux:select wire:model.live="format">
                        <flux:option value="round-robin">Round-Robin</flux:option>
                        <flux:option value="knockout">Knockout / Eliminasi</flux:option>
                    </flux:select>
                    <flux:description>
                        @if ($format === 'knockout')
                            Babak 1 saja. Babak selanjutnya dibuat manual setelah hasil keluar.
                        @else
                            Setiap tim bertemu satu sama lain sekali.
                        @endif
                    </flux:description>
                </flux:field>

                {{-- Team count summary --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between">
                        <flux:text class="text-sm text-slate-600 dark:text-slate-400">Tim Approved</flux:text>
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $this->approvedTeams->count() }}
                        </span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <flux:text class="text-sm text-slate-600 dark:text-slate-400">Total Pertandingan</flux:text>
                        @php
                            $n = $this->approvedTeams->count();
                            if ($format === 'knockout') {
                                $total = (int) floor($n / 2); // Babak 1 saja, BYE tidak dihitung sbg match
                            } else {
                                $total = $n >= 2 ? ($n * ($n - 1)) / 2 : 0;
                            }
                        @endphp
                        <span class="text-2xl font-bold text-slate-700 dark:text-slate-300">{{ $total }}</span>
                    </div>
                    @if ($format === 'knockout' && $n % 2 !== 0)
                        <div class="mt-2 flex items-center justify-between">
                            <flux:text class="text-sm text-slate-600 dark:text-slate-400">Tim BYE</flux:text>
                            <span class="text-sm font-semibold text-amber-500">1</span>
                        </div>
                    @endif
                </div>

                {{-- Validation error --}}
                @error('generate')
                    <flux:callout variant="danger" icon="exclamation-triangle">
                        {{ $message }}
                    </flux:callout>
                @enderror

                {{-- Existing fixtures warning --}}
                @if ($this->existingFixturesCount > 0)
                    <flux:callout variant="warning" icon="exclamation-triangle">
                        <flux:callout.heading>Jadwal sudah ada</flux:callout.heading>
                        <flux:callout.text>
                            Ada {{ $this->existingFixturesCount }} pertandingan terjadwal. Generate ulang akan menambahkan jadwal baru di atasnya.
                        </flux:callout.text>
                    </flux:callout>
                @endif

                <flux:button
                    wire:click="generate"
                    wire:loading.attr="disabled"
                    variant="primary"
                    icon="bolt"
                    class="w-full"
                    :disabled="$this->approvedTeams->count() < 2"
                >
                    <span wire:loading.remove wire:target="generate">Generate Jadwal</span>
                    <span wire:loading wire:target="generate">Memproses...</span>
                </flux:button>
            </flux:card>
        </div>

        {{-- Right: Approved Teams Preview --}}
        <div class="lg:col-span-2">
            <flux:card class="space-y-4">
                <flux:heading size="lg">Tim yang Akan Dijadwalkan</flux:heading>
                <flux:separator />

                @if ($this->approvedTeams->isEmpty())
                    <div class="flex flex-col items-center gap-3 py-10 text-center">
                        <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <flux:text class="text-slate-400">Belum ada tim berstatus Approved.</flux:text>
                        <flux:button href="{{ route('admin.teams') }}" wire:navigate variant="ghost" size="sm" icon="arrow-left">
                            Kelola Tim
                        </flux:button>
                    </div>
                @else
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($this->approvedTeams as $team)
                            <div wire:key="team-{{ $team->id }}"
                                 class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/30">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-xs font-bold text-white shadow-sm select-none">
                                    {{ strtoupper(substr($team->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $team->name }}</p>
                                    <p class="truncate text-xs text-slate-400">{{ $team->sport_type }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Preview matchup count info --}}
                    @if ($this->approvedTeams->count() >= 2)
                        <flux:text class="text-center text-xs text-slate-400">
                            @if ($format === 'knockout')
                                {{ $this->approvedTeams->count() }} tim → seeding acak → Babak 1 = <strong>{{ $total }}</strong> pertandingan
                                @if ($n % 2 !== 0)
                                    + <strong>1 BYE</strong>
                                @endif
                            @else
                                {{ $this->approvedTeams->count() }} tim &times; formula Round-Robin = <strong>{{ $total }}</strong> pertandingan
                            @endif
                        </flux:text>
                    @endif
                @endif
            </flux:card>
        </div>

    </div>

</div>
