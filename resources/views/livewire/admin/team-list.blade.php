<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Daftar Tim Peserta</flux:heading>
            <flux:text class="mt-1 text-slate-500">Semua tim yang telah mendaftar ke turnamen.</flux:text>
        </div>
        {{-- Summary count --}}
        @if ($teams->total() > 0)
            <div class="flex items-center gap-2 rounded-xl border border-[#c8e87d] bg-[#E4FD97] px-4 py-2 dark:border-[rgba(228,253,151,0.25)] dark:bg-[#2a3d1a]">
                <svg class="h-4 w-4 text-[#4a7c30] dark:text-[#E4FD97]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm font-semibold text-[#1e2b1d] dark:text-[#E4FD97]">{{ $teams->total() }} Tim</span>
            </div>
        @endif
    </div>

    {{-- Filters --}}
    <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama tim..."
                icon="magnifying-glass"
                class="sm:max-w-xs"
            />

            <select wire:model.live="statusFilter" class="sm:w-44 w-full rounded-lg border border-[#c8e87d] bg-[#E4FD97] dark:bg-[#2a3d1a] px-3 py-2 text-sm text-black focus:outline-none focus:ring-2 focus:ring-[#4a7c30]">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="disqualified">Disqualified</option>
            </select>

            @if ($search || $statusFilter !== 'all')
                <flux:badge color="blue" class="self-start sm:self-auto">
                    {{ $teams->total() }} hasil ditemukan
                </flux:badge>
            @endif
        </div>
    </flux:card>

    {{-- Team Cards Grid --}}
    @if ($teams->isEmpty())
        <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-16">
            <div class="flex flex-col items-center gap-3 text-center">
                <svg class="h-12 w-12 text-[#4a7c30]/40 dark:text-[#E4FD97]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <flux:text class="text-[#1e2b1d] dark:text-[#E4FD97]/70">
                    {{ $search || $statusFilter !== 'all' ? 'Tidak ada tim yang cocok dengan filter.' : 'Belum ada tim yang mendaftar.' }}
                </flux:text>
            </div>
        </flux:card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($teams as $team)
                <div wire:key="team-{{ $team->id }}"
                     class="group relative flex flex-col rounded-2xl border-2 bg-[#E4FD97] shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 dark:bg-[#2a3d1a]
                        {{ $team->status === 'approved'     ? 'border-green-400 dark:border-green-600' : '' }}
                        {{ $team->status === 'pending'      ? 'border-amber-400 dark:border-amber-500' : '' }}
                        {{ $team->status === 'disqualified' ? 'border-red-400 dark:border-red-600'     : '' }}
                     ">

                    <div class="flex flex-1 flex-col gap-4 p-5">

                        {{-- Avatar + Nama + Invoice --}}
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-sm font-bold text-white shadow-md select-none">
                                {{ strtoupper(substr($team->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-base font-bold text-[#1e2b1d] dark:text-white">{{ $team->name }}</p>
                                @if ($team->invoice_number)
                                    <p class="font-mono text-xs text-[#4a7c30]/70 dark:text-[#E4FD97]/50">{{ $team->invoice_number }}</p>
                                @endif
                            </div>
                            {{-- Status badge --}}
                            <x-status-badge :status="$team->status" type="team" size="sm" class="shrink-0" />
                        </div>

                        {{-- Info rows --}}
                        <div class="space-y-2 rounded-xl border border-[#c8e87d]/60 bg-white/50 p-3 dark:border-[rgba(228,253,151,0.1)] dark:bg-black/10">

                            {{-- Contact Person --}}
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 shrink-0 text-[#4a7c30]/60 dark:text-[#E4FD97]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="truncate text-xs text-[#1e2b1d] dark:text-slate-300">{{ $team->contact_person }}</span>
                            </div>

                            {{-- Phone --}}
                            @if ($team->phone)
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 shrink-0 text-[#4a7c30]/60 dark:text-[#E4FD97]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-xs text-[#1e2b1d] dark:text-slate-300">{{ $team->phone }}</span>
                            </div>
                            @endif

                            {{-- Cabang & PAID row --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-[#4a7c30]/60 dark:text-[#E4FD97]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <flux:badge color="zinc" size="sm">{{ $team->sport_type }}</flux:badge>
                                </div>

                                @if ($team->payment_status === 'paid')
                                    <div class="flex items-center gap-1 text-green-700 dark:text-green-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-xs font-semibold">Lunas</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span class="text-xs font-semibold">Belum Lunas</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Tanggal Daftar --}}
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 shrink-0 text-[#4a7c30]/60 dark:text-[#E4FD97]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs text-[#1e2b1d]/60 dark:text-slate-400">{{ $team->registered_at->format('d M Y, H:i') }}</span>
                            </div>

                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-1">
                            <flux:button
                                href="{{ route('admin.teams.show', $team) }}"
                                wire:navigate
                                size="sm"
                                variant="ghost"
                                icon="eye"
                                class="flex-1"
                            >
                                Detail
                            </flux:button>

                            @if ($team->status === 'pending')
                                <flux:button
                                    wire:click="openConfirmModal({{ $team->id }}, 'approve')"
                                    size="sm"
                                    variant="primary"
                                    icon="check"
                                    class="flex-1"
                                >
                                    Approve
                                </flux:button>
                            @endif

                            @if ($team->status !== 'disqualified')
                                <flux:button
                                    wire:click="openConfirmModal({{ $team->id }}, 'disqualify')"
                                    size="sm"
                                    variant="danger"
                                    icon="x-mark"
                                    title="Disqualify"
                                />
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($teams->hasPages())
            <div class="flex justify-center">
                {{ $teams->links() }}
            </div>
        @endif

        {{-- Summary footer --}}
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
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="$set('showConfirmModal', false)"
                >
                    Batal
                </flux:button>

                <flux:button
                    type="button"
                    wire:click="executeAction"
                    :variant="$confirmAction === 'approve' ? 'primary' : 'danger'"
                >
                    {{ $confirmAction === 'approve' ? 'Ya, Approve' : 'Ya, Disqualify' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
