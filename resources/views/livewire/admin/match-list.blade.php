<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Daftar Pertandingan</flux:heading>
            <flux:text class="mt-1 text-slate-500">Kelola venue, jadwal, dan wasit tiap pertandingan.</flux:text>
        </div>
        <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="ghost" icon="bolt" size="sm">
            Generator Jadwal
        </flux:button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama tim..."
            icon="magnifying-glass"
            class="sm:max-w-xs"
        />

        <flux:select wire:model.live="statusFilter" class="sm:w-44">
            <flux:option value="all">Semua Status</flux:option>
            <flux:option value="scheduled">Scheduled</flux:option>
            <flux:option value="ongoing">Ongoing</flux:option>
            <flux:option value="done">Done</flux:option>
            <flux:option value="cancelled">Cancelled</flux:option>
        </flux:select>

        @if ($search || $statusFilter !== 'all')
            <flux:badge color="blue" class="self-start sm:self-auto">
                {{ $matches->total() }} hasil
            </flux:badge>
        @endif
    </div>

    {{-- Matches Table --}}
    <flux:card class="overflow-hidden p-0">
        <flux:table>
            <flux:columns>
                <flux:column>Pertandingan</flux:column>
                <flux:column>Babak</flux:column>
                <flux:column>Tanggal & Waktu</flux:column>
                <flux:column>Venue</flux:column>
                <flux:column>Wasit</flux:column>
                <flux:column>Status</flux:column>
                <flux:column class="text-right">Aksi</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($matches as $match)
                    <flux:row wire:key="match-{{ $match->id }}">

                        {{-- Pertandingan --}}
                        <flux:cell>
                            @if ($match->notes && str_contains($match->notes, 'BYE'))
                                {{-- BYE entry --}}
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $match->team1->name }}
                                    </span>
                                    <flux:badge color="amber" size="sm">BYE</flux:badge>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[8rem]">
                                        {{ $match->team1->name }}
                                    </span>
                                    <span class="shrink-0 text-xs font-bold text-slate-400">VS</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[8rem]">
                                        {{ $match->team2->name }}
                                    </span>
                                </div>
                            @endif
                        </flux:cell>

                        {{-- Babak --}}
                        <flux:cell>
                            <flux:badge color="zinc" size="sm">{{ $match->round ?? '—' }}</flux:badge>
                        </flux:cell>

                        {{-- Tanggal & Waktu --}}
                        <flux:cell>
                            @if ($match->match_date)
                                <p class="text-sm text-slate-700 dark:text-slate-300">
                                    {{ $match->match_date->format('d M Y') }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $match->match_date->format('H:i') }}</p>
                            @else
                                <span class="text-sm text-slate-400">—</span>
                            @endif
                        </flux:cell>

                        {{-- Venue --}}
                        <flux:cell>
                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ $match->venue ?? '—' }}
                            </span>
                        </flux:cell>

                        {{-- Wasit --}}
                        <flux:cell>
                            <span class="text-sm text-slate-700 dark:text-slate-300">
                                {{ $match->referee ?? '—' }}
                            </span>
                        </flux:cell>

                        {{-- Status --}}
                        <flux:cell>
                            <x-status-badge :status="$match->status" type="match" />
                        </flux:cell>

                        {{-- Aksi --}}
                        <flux:cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- BYE entries and done/cancelled matches still allow venue edit --}}
                                @if (! ($match->notes && str_contains($match->notes, 'BYE')))
                                    <flux:button
                                        href="{{ route('admin.matches.control', $match) }}"
                                        wire:navigate
                                        size="sm"
                                        variant="primary"
                                        icon="play"
                                    >
                                        Kelola
                                    </flux:button>
                                @endif

                                <flux:button
                                    wire:click="openEdit({{ $match->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil-square"
                                >
                                    Edit Detail
                                </flux:button>
                            </div>
                        </flux:cell>

                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <flux:text class="text-slate-400">
                                    {{ $search || $statusFilter !== 'all' ? 'Tidak ada pertandingan yang cocok.' : 'Belum ada pertandingan. Generate jadwal terlebih dahulu.' }}
                                </flux:text>
                                @if (! $search && $statusFilter === 'all')
                                    <flux:button href="{{ route('admin.fixtures') }}" wire:navigate variant="ghost" size="sm" icon="bolt">
                                        Generator Jadwal
                                    </flux:button>
                                @endif
                            </div>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>

        @if ($matches->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-slate-700">
                {{ $matches->links() }}
            </div>
        @endif
    </flux:card>

    @if ($matches->total() > 0)
        <flux:text class="text-center text-xs text-slate-400">
            Menampilkan {{ $matches->firstItem() }}–{{ $matches->lastItem() }} dari {{ $matches->total() }} pertandingan
        </flux:text>
    @endif

    {{-- Edit Detail Modal --}}
    <flux:modal wire:model.self="showEditModal" class="md:w-[28rem]">
        <form wire:submit="saveEdit" class="space-y-5">
            <div>
                <flux:heading size="lg">Edit Detail Pertandingan</flux:heading>
                <flux:text class="mt-1 text-slate-500">Update venue, waktu, dan wasit. Skor & status dikelola di halaman Live Score.</flux:text>
            </div>

            <flux:field>
                <flux:label>Venue / Lapangan</flux:label>
                <flux:input
                    wire:model="editVenue"
                    type="text"
                    placeholder="cth. Lapangan A, GOR Serbaguna"
                    maxlength="200"
                />
                <flux:error name="editVenue" />
            </flux:field>

            <flux:field>
                <flux:label>Tanggal & Waktu</flux:label>
                <flux:input
                    wire:model="editMatchDate"
                    type="datetime-local"
                />
                <flux:error name="editMatchDate" />
            </flux:field>

            <flux:field>
                <flux:label>Nama Wasit</flux:label>
                <flux:input
                    wire:model="editReferee"
                    type="text"
                    placeholder="cth. Budi Santoso"
                    maxlength="100"
                />
                <flux:error name="editReferee" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-1">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

</div>
