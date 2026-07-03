<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Produk POS</flux:heading>
            <flux:text class="mt-1 text-slate-500">Kelola produk kantin dan merchandise untuk event.</flux:text>
        </div>
        @if(auth()->user()->isAdmin())
            <flux:button wire:click="openCreate" variant="primary" icon="plus">
                Tambah Produk
            </flux:button>
        @endif
    </div>

    {{-- Search + Summary --}}
    <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama produk..."
                icon="magnifying-glass"
                class="sm:max-w-xs"
            />
            @if ($products->total() > 0)
                <div class="flex items-center gap-3 text-xs text-[#1e2b1d]/60 dark:text-[#E4FD97]/50">
                    <span class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        {{ $products->total() }} produk
                    </span>
                </div>
            @endif
        </div>
    </flux:card>

    {{-- Product Grid --}}
    @if ($products->isEmpty())
        <flux:card class="secondary-card bg-[#E4FD97] border-[#c8e87d] py-16">
            <div class="flex flex-col items-center gap-3 text-center">
                <svg class="h-12 w-12 text-[#4a7c30]/30 dark:text-[#E4FD97]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <flux:text class="text-[#1e2b1d] dark:text-[#E4FD97]/70">
                    {{ $search ? 'Tidak ada produk yang cocok.' : 'Belum ada produk. Tambahkan produk pertama.' }}
                </flux:text>
                @if (! $search && auth()->user()->isAdmin())
                    <flux:button wire:click="openCreate" variant="primary" icon="plus" size="sm">
                        Tambah Produk
                    </flux:button>
                @endif
            </div>
        </flux:card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                <div wire:key="product-{{ $product->id }}"
                     class="group flex flex-col rounded-2xl border-2 bg-[#E4FD97] shadow-sm transition-all duration-150 hover:shadow-md hover:-translate-y-0.5 dark:bg-[#2a3d1a]
                        {{ $product->stock === 0   ? 'border-red-400 dark:border-red-600' : ($product->stock <= 5 ? 'border-amber-400 dark:border-amber-500' : 'border-[#c8e87d] dark:border-[rgba(228,253,151,0.25)]') }}">

                    {{-- Card Body --}}
                    <div class="flex flex-1 flex-col gap-4 p-5">

                        {{-- Icon + Name --}}
                        <div class="flex items-start gap-3">
                            {{-- Product icon --}}
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-600 text-white shadow-sm">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold text-[#1e2b1d] dark:text-white">
                                    {{ $product->product_name }}
                                </p>
                                {{-- Stock badge --}}
                                <div class="mt-1">
                                    @if ($product->stock === 0)
                                        <flux:badge color="red" size="sm">Stok Habis</flux:badge>
                                    @elseif ($product->stock <= 5)
                                        <flux:badge color="yellow" size="sm">Stok Menipis · {{ $product->stock }}</flux:badge>
                                    @else
                                        <flux:badge color="green" size="sm">Tersedia · {{ $product->stock }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="rounded-xl bg-white/50 px-4 py-3 dark:bg-black/10">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-[#4a7c30]/50 dark:text-[#E4FD97]/40">Harga</p>
                            <p class="mt-0.5 text-xl font-black tabular-nums text-[#1e2b1d] dark:text-white">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>

                    </div>

                    {{-- Actions --}}
                    @if(auth()->user()->isAdmin())
                        <div class="flex items-center gap-2 border-t border-[#c8e87d]/50 px-5 py-3 dark:border-[rgba(228,253,151,0.1)]">
                            <flux:button
                                wire:click="openEdit({{ $product->id }})"
                                size="sm"
                                variant="ghost"
                                icon="pencil-square"
                                class="flex-1"
                            >
                                Edit
                            </flux:button>
                            <flux:button
                                wire:click="openDelete({{ $product->id }})"
                                size="sm"
                                variant="danger"
                                icon="trash"
                                title="Hapus"
                            />
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="flex justify-center">
                {{ $products->links() }}
            </div>
        @endif

        <flux:text class="text-center text-xs text-slate-400">
            Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk
        </flux:text>
    @endif

    @if(auth()->user()->isAdmin())
    {{-- Create / Edit Modal --}}
    <flux:modal wire:model.self="showFormModal" class="md:w-[28rem]">
        <form wire:submit="save" class="space-y-5">
            <div>
                <flux:heading size="lg">
                    {{ $editingProductId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </flux:heading>
                <flux:text class="mt-1 text-slate-500">
                    {{ $editingProductId ? 'Perbarui detail produk.' : 'Isi data produk baru.' }}
                </flux:text>
            </div>
            <flux:field>
                <flux:label>Nama Produk</flux:label>
                <flux:input wire:model="formProductName" type="text" placeholder="cth. Air Mineral 600ml" maxlength="100" autofocus/>
                <flux:error name="formProductName"/>
            </flux:field>
            <flux:field>
                <flux:label>Harga (Rp)</flux:label>
                <flux:input wire:model="formPrice" type="number" min="0" step="any" placeholder="cth. 5000" inputmode="numeric"/>
                <flux:description>Masukkan harga dalam Rupiah, tanpa titik atau koma.</flux:description>
                <flux:error name="formPrice"/>
            </flux:field>
            <flux:field>
                <flux:label>Stok</flux:label>
                <flux:input wire:model="formStock" type="number" min="0" placeholder="cth. 50" inputmode="numeric"/>
                <flux:error name="formStock"/>
            </flux:field>
            <div class="flex justify-end gap-3 pt-1">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check">
                    {{ $editingProductId ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model.self="showDeleteModal" class="md:w-[24rem]" :dismissible="false">
        <div class="space-y-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg">Hapus Produk?</flux:heading>
                    <flux:text class="mt-1 text-slate-500">
                        Produk <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $deletingProductName }}</span>
                        akan dihapus permanen. Data transaksi yang sudah ada tidak terpengaruh.
                    </flux:text>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">Batal</flux:button>
                <flux:button wire:click="destroy" variant="danger" icon="trash">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
    @endif

</div>
