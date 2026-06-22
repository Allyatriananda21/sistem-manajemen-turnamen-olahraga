<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.teams') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tim</a>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.teams.show', $team) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $team->name }}</a>
        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-slate-700 dark:text-slate-300">Pemain</span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Pemain — {{ $team->name }}</flux:heading>
            <flux:text class="mt-1 text-slate-500">Kelola daftar pemain terdaftar untuk tim ini.</flux:text>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Tambah Pemain
        </flux:button>
    </div>

    {{-- Players Table --}}
    <flux:card class="overflow-hidden p-0">
        <flux:table>
            <flux:columns>
                <flux:column class="w-12 text-center">#</flux:column>
                <flux:column>Nama Pemain</flux:column>
                <flux:column>Posisi</flux:column>
                <flux:column class="text-right">Aksi</flux:column>
            </flux:columns>
            <flux:rows>
                @forelse ($players as $player)
                    <flux:row wire:key="player-{{ $player->id }}">
                        <flux:cell class="text-center">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $player->jersey_number ?? '—' }}
                            </span>
                        </flux:cell>
                        <flux:cell>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $player->full_name }}</span>
                        </flux:cell>
                        <flux:cell>
                            <span class="text-sm text-slate-500">{{ $player->position ?? '—' }}</span>
                        </flux:cell>
                        <flux:cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="openEdit({{ $player->id }})" size="sm" variant="ghost" icon="pencil-square">Edit</flux:button>
                                <flux:button wire:click="openDelete({{ $player->id }})" size="sm" variant="danger" icon="trash">Hapus</flux:button>
                            </div>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="4" class="py-12 text-center">
                            <flux:text class="text-slate-400">Belum ada pemain. Tambahkan pemain pertama.</flux:text>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>
        @if ($players->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-slate-700">{{ $players->links() }}</div>
        @endif
    </flux:card>

    {{-- Create / Edit Modal --}}
    <flux:modal wire:model.self="showFormModal" class="md:w-[26rem]">
        <form wire:submit="save" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ $editingPlayerId ? 'Edit Pemain' : 'Tambah Pemain' }}</flux:heading>
            </div>
            <flux:field>
                <flux:label>Nama Lengkap</flux:label>
                <flux:input wire:model="formFullName" type="text" placeholder="cth. Ahmad Rizki" autofocus maxlength="100" />
                <flux:error name="formFullName" />
            </flux:field>
            <flux:field>
                <flux:label>Nomor Punggung</flux:label>
                <flux:input wire:model="formJerseyNumber" type="number" min="1" max="99" placeholder="cth. 10" />
                <flux:error name="formJerseyNumber" />
            </flux:field>
            <flux:field>
                <flux:label>Posisi</flux:label>
                <flux:input wire:model="formPosition" type="text" placeholder="cth. Penyerang, Kiper" maxlength="50" />
                <flux:error name="formPosition" />
            </flux:field>
            <div class="flex justify-end gap-3 pt-1">
                <flux:modal.close><flux:button type="button" variant="ghost">Batal</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check">{{ $editingPlayerId ? 'Perbarui' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Modal --}}
    <flux:modal wire:model.self="showDeleteModal" class="md:w-[24rem]" :dismissible="false">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Hapus Pemain?</flux:heading>
                <flux:text class="mt-1 text-slate-500">
                    Pemain <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $deletingPlayerName }}</span> beserta seluruh statistiknya akan dihapus.
                </flux:text>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">Batal</flux:button>
                <flux:button wire:click="destroy" variant="danger" icon="trash">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

</div>
