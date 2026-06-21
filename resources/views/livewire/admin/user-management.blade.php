<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Manajemen User</flux:heading>
            <flux:text class="mt-1 text-slate-500">Kelola akun panitia, wasit, dan kasir event.</flux:text>
        </div>
        <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
            Tambah User
        </flux:button>
    </div>

    {{-- Search --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Cari nama atau email..."
        icon="magnifying-glass"
        class="max-w-sm"
    />

    {{-- Users Table --}}
    <flux:card class="overflow-hidden p-0">
        <flux:table>
            <flux:columns>
                <flux:column>Nama</flux:column>
                <flux:column>Email</flux:column>
                <flux:column>Role</flux:column>
                <flux:column>Status</flux:column>
                <flux:column class="text-right">Aksi</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($users as $user)
                    <flux:row wire:key="user-{{ $user->id }}">
                        {{-- Nama --}}
                        <flux:cell>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-xs font-bold text-white shadow-sm select-none">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    @if ($user->id === auth()->id())
                                        <flux:badge size="sm" color="blue" inset="top bottom">Anda</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </flux:cell>

                        {{-- Email --}}
                        <flux:cell>
                            <flux:text class="text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</flux:text>
                        </flux:cell>

                        {{-- Role (editable select) --}}
                        <flux:cell>
                            <select
                                wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                :disabled="$user->id === auth()->id()"
                                class="w-28 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs py-1.5 px-2 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="wasit" {{ $user->role === 'wasit' ? 'selected' : '' }}>Wasit</option>
                                <option value="kasir" {{ $user->role === 'kasir' ? 'selected' : '' }}>Kasir</option>
                            </select>
                        </flux:cell>

                        {{-- Status aktif --}}
                        <flux:cell>
                            @if ($user->is_active)
                                <flux:badge color="green">Aktif</flux:badge>
                            @else
                                <flux:badge color="red">Nonaktif</flux:badge>
                            @endif
                        </flux:cell>

                        {{-- Aksi --}}
                        <flux:cell class="text-right">
                            <flux:button
                                wire:click="toggleActive({{ $user->id }})"
                                wire:confirm="{{ $user->is_active ? 'Nonaktifkan user ini?' : 'Aktifkan user ini?' }}"
                                size="sm"
                                :variant="$user->is_active ? 'danger' : 'primary'"
                                :disabled="$user->id === auth()->id()"
                            >
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </flux:button>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="5" class="py-12 text-center">
                            <flux:text class="text-slate-400">
                                {{ $search ? 'Tidak ada user yang cocok dengan pencarian.' : 'Belum ada user.' }}
                            </flux:text>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-slate-700">
                {{ $users->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Create User Modal --}}
    <flux:modal wire:model="showCreateModal" class="md:w-[30rem]">
        <form wire:submit="store" class="space-y-5">
            <div>
                <flux:heading size="lg">Tambah User Baru</flux:heading>
                <flux:text class="mt-1 text-slate-500">Buat akun panitia, wasit, atau kasir.</flux:text>
            </div>

            <flux:field>
                <flux:label>Nama Lengkap</flux:label>
                <flux:input wire:model="name" type="text" placeholder="Nama lengkap" autofocus />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="email" type="email" placeholder="email@contoh.com" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Password</flux:label>
                <flux:input wire:model="password" type="password" placeholder="Min. 8 karakter" viewable />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>Role</flux:label>
                <select wire:model="role" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="admin">Admin</option>
                    <option value="wasit">Wasit</option>
                    <option value="kasir">Kasir</option>
                </select>
                <flux:error name="role" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showCreateModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

</div>
