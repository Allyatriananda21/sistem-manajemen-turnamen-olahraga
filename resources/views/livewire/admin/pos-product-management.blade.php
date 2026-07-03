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

    {{-- Search --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Cari nama produk..."
        icon="magnifying-glass"
        class="max-w-sm"
    />

    {{-- Products Table --}}
    <flux:card class="secondary-card overflow-hidden p-0 bg-[#E4FD97] border-[#c8e87d]">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nama Produk</flux:table.column>
                <flux:table.column class="text-right">Harga</flux:table.column>
                <flux:table.column class="text-center">Stok</flux:table.column>
                @if(auth()->user()->isAdmin())
                <flux:table.column class="text-right">Aksi</flux:table.column>
                @endif
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($products as $product)
                    <flux:table.row wire:key="product-{{ $product->id }}">

                        {{-- Nama --}}
                        <flux:table.cell>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                {{ $product->product_name }}
                            </span>
                        </flux:table.cell>

                        {{-- Harga --}}
                        <flux:table.cell class="text-right">
                            <span class="text-sm tabular-nums text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                        </flux:table.cell>

                        {{-- Stok --}}
                        <flux:table.cell class="text-center">
                            @if ($product->stock === 0)
                                <flux:badge color="red" size="sm">Habis</flux:badge>
                            @elseif ($product->stock <= 5)
                                <flux:badge color="yellow" size="sm">{{ $product->stock }}</flux:badge>
                            @else
                                <flux:badge color="green" size="sm">{{ $product->stock }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        {{-- Aksi --}}
                        @if(auth()->user()->isAdmin())
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button
                                    wire:click="openEdit({{ $product->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil-square"
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    wire:click="openDelete({{ $product->id }})"
                                    size="sm"
                                    variant="danger"
                                    icon="trash"
                                >
                                    Hapus
                                </flux:button>
                            </div>
                        </flux:table.cell>
                        @endif

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <flux:text class="text-slate-400">
                                    {{ $search ? 'Tidak ada produk yang cocok.' : 'Belum ada produk. Tambahkan produk pertama.' }}
                                </flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($products->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-slate-700">
                {{ $products->links() }}
            </div>
        @endif
    </flux:card>

    @if ($products->total() > 0)
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
                <flux:input
                    wire:model="formProductName"
                    type="text"
                    placeholder="cth. Air Mineral 600ml"
                    maxlength="100"
                    autofocus
                />
                <flux:error name="formProductName" />
            </flux:field>

            <flux:field>
                <flux:label>Harga (Rp)</flux:label>
                <flux:input
                    wire:model="formPrice"
                    type="number"
                    min="0"
                    step="any"
                    placeholder="cth. 5000"
                    inputmode="numeric"
                />
                <flux:description>Masukkan harga dalam Rupiah, tanpa titik atau koma.</flux:description>
                <flux:error name="formPrice" />
            </flux:field>

            <flux:field>
                <flux:label>Stok</flux:label>
                <flux:input
                    wire:model="formStock"
                    type="number"
                    min="0"
                    placeholder="cth. 50"
                    inputmode="numeric"
                />
                <flux:error name="formStock" />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="$set('showDeleteModal', false)"
                >
                    Batal
                </flux:button>
                <flux:button wire:click="destroy" variant="danger" icon="trash">
                    Ya, Hapus
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif

</div>
