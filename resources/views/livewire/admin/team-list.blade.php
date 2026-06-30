<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl">Daftar Tim Peserta</flux:heading>
        <flux:text class="mt-1 text-slate-500">Semua tim yang telah mendaftar ke turnamen.</flux:text>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama tim..."
            icon="magnifying-glass"
            class="sm:max-w-xs"
        />

        <select wire:model.live="statusFilter" class="sm:w-44 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="all">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="disqualified">Disqualified</option>
        </select>

        {{-- Active filter badge --}}
        @if ($search || $statusFilter !== 'all')
            <flux:badge color="blue" class="self-start sm:self-auto">
                {{ $teams->total() }} hasil ditemukan
            </flux:badge>
        @endif
    </div>

    {{-- Teams Table --}}
    <flux:card class="overflow-hidden p-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable>Nama Tim</flux:table.column>
                <flux:table.column>Contact Person</flux:table.column>
                <flux:table.column>Cabang Olahraga</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>PAID</flux:table.column>
                <flux:table.column>Terdaftar</flux:table.column>
                <flux:table.column class="text-right">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($teams as $team)
                    <flux:table.row wire:key="team-{{ $team->id }}">

                        {{-- Nama Tim --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-xs font-bold text-white shadow-sm select-none">
                                    {{ strtoupper(substr($team->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $team->name }}</p>
                                    @if ($team->invoice_number)
                                        <p class="text-xs text-slate-400">{{ $team->invoice_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        {{-- Contact Person --}}
                        <flux:table.cell>
                            <div>
                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $team->contact_person }}</p>
                                <p class="text-xs text-slate-400">{{ $team->phone }}</p>
                            </div>
                        </flux:table.cell>

                        {{-- Cabang Olahraga --}}
                        <flux:table.cell>
                            <flux:badge color="zinc" size="sm">{{ $team->sport_type }}</flux:badge>
                        </flux:table.cell>

                        {{-- Status --}}
                        <flux:table.cell>
                            <x-status-badge :status="$team->status" type="team" />
                        </flux:table.cell>

                        {{-- PAID --}}
                        <flux:table.cell>
                            @if ($team->payment_status === 'paid')
                                <div class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm font-semibold">Yes</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 text-red-500 dark:text-red-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="text-sm font-semibold">No</span>
                                </div>
                            @endif
                        </flux:table.cell>

                        {{-- Tanggal Daftar --}}
                        <flux:table.cell>
                            <flux:text class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $team->registered_at->format('d M Y, H:i') }}
                            </flux:text>
                        </flux:table.cell>

                        {{-- Aksi --}}
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Lihat Detail --}}
                                <flux:button
                                    href="{{ route('admin.teams.show', $team) }}"
                                    wire:navigate
                                    size="sm"
                                    variant="ghost"
                                    icon="eye"
                                >
                                    Detail
                                </flux:button>

                                {{-- Approve: hanya tampil kalau status masih pending --}}
                                @if ($team->status === 'pending')
                                    <flux:button
                                        wire:click="confirmAction({{ $team->id }}, 'approve')"
                                        size="sm"
                                        variant="primary"
                                        icon="check"
                                    >
                                        Approve
                                    </flux:button>
                                @endif

                                {{-- Disqualify: tampil selama belum disqualified --}}
                                @if ($team->status !== 'disqualified')
                                    <flux:button
                                        wire:click="confirmAction({{ $team->id }}, 'disqualify')"
                                        size="sm"
                                        variant="danger"
                                        icon="x-mark"
                                    >
                                        Disqualify
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <flux:text class="text-slate-400">
                                    {{ $search || $statusFilter !== 'all' ? 'Tidak ada tim yang cocok dengan filter.' : 'Belum ada tim yang mendaftar.' }}
                                </flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($teams->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-slate-700">
                {{ $teams->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Summary footer --}}
    @if ($teams->total() > 0)
        <flux:text class="text-center text-xs text-slate-400">
            Menampilkan {{ $teams->firstItem() }}–{{ $teams->lastItem() }} dari {{ $teams->total() }} tim
        </flux:text>
    @endif

    {{-- Confirmation Modal --}}
    <flux:modal wire:model.self="showConfirmModal" class="md:w-[26rem]" :dismissible="false">
        <div class="space-y-5">
            <div>
                @if ($confirmAction === 'approve')
                    <flux:heading size="lg">Approve Tim?</flux:heading>
                    <flux:text class="mt-2 text-slate-500">
                        Tim <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $confirmTeamName }}</span>
                        akan disetujui dan statusnya berubah menjadi <span class="font-semibold text-green-600">Approved</span>.
                    </flux:text>
                @else
                    <flux:heading size="lg">Disqualify Tim?</flux:heading>
                    <flux:text class="mt-2 text-slate-500">
                        Tim <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $confirmTeamName }}</span>
                        akan didiskualifikasi. Aksi ini dapat diubah kembali jika diperlukan.
                    </flux:text>
                @endif
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="$set('showConfirmModal', false)">
                        Batal
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    wire:click="executeAction"
                    :variant="$confirmAction === 'approve' ? 'primary' : 'danger'"
                >
                    {{ $confirmAction === 'approve' ? 'Ya, Approve' : 'Ya, Disqualify' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
