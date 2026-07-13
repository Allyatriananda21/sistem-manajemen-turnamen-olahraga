<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="text-[#4a7c30] dark:text-[#E4FD97]">Kasir POS</flux:heading>
            <flux:text class="mt-0.5 text-slate-500">Cari produk, tambahkan ke keranjang, hitung kembalian.</flux:text>
        </div>
        <flux:button href="{{ route('admin.pos.products') }}" wire:navigate variant="ghost" size="sm" icon="squares-2x2">
            Kelola Produk
        </flux:button>
    </div>

    {{-- Tab switcher (Alpine-driven, no server roundtrip) --}}
    <div x-data="{ tab: 'retail' }">

        {{-- Tab buttons --}}
        <div class="flex gap-1 rounded-xl border border-slate-200 bg-slate-100 p-1 dark:border-slate-700 dark:bg-slate-800 sm:w-fit">
            <button
                @click="tab = 'retail'"
                :class="tab === 'retail' ? 'bg-white dark:bg-slate-900 shadow-sm text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm transition-all"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Retail
            </button>
            <button
                @click="tab = 'tagihan'"
                :class="tab === 'tagihan' ? 'bg-white dark:bg-slate-900 shadow-sm text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm transition-all"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Tagihan Tim
                @if ($this->unpaidTeams->isNotEmpty())
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">
                        {{ $this->unpaidTeams->count() }}
                    </span>
                @endif
            </button>
            <button
                @click="tab = 'denda'"
                :class="tab === 'denda' ? 'bg-white dark:bg-slate-900 shadow-sm text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm transition-all"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Denda Tim
            </button>
        </div>

        {{-- ================================================================
             TAB: RETAIL
             ================================================================ --}}
        <div x-show="tab === 'retail'" class="mt-4">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        {{-- ============================================================
             LEFT: Product Search + Grid
             ============================================================ --}}
        <div class="space-y-4 lg:col-span-3">

            {{-- Search --}}
            <flux:input
                wire:model.live.debounce.250ms="search"
                placeholder="Ketik nama produk untuk mencari..."
                icon="magnifying-glass"
                autofocus
            />

            {{-- Product Grid --}}
            @if ($this->products->isEmpty())
                <div class="flex flex-col items-center gap-3 rounded-2xl border border-dashed border-slate-200 py-16 dark:border-slate-700">
                    <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <flux:text class="text-center text-slate-400">
                        {{ $search ? 'Tidak ada produk yang cocok.' : 'Tidak ada produk tersedia (stok habis).' }}
                    </flux:text>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($this->products as $product)
                        @php
                            $inCart    = isset($cart[$product->id]);
                            $cartQty   = $inCart ? $cart[$product->id]['qty'] : 0;
                            $maxStock  = $product->stock;
                            $atMax     = $inCart && $cartQty >= $maxStock;
                        @endphp
                        <button
                            wire:click="addToCart({{ $product->id }})"
                            wire:key="product-{{ $product->id }}"
                            @disabled($atMax)
                            class="group relative flex flex-col gap-2 rounded-2xl border p-4 text-left transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400
                                {{ $inCart
                                    ? 'border-indigo-300 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/20'
                                    : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/50 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-indigo-700' }}
                                {{ $atMax ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}"
                        >
                            {{-- Cart qty badge --}}
                            @if ($inCart)
                                <span class="absolute right-2 top-2 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-bold text-white">
                                    {{ $cartQty }}
                                </span>
                            @endif

                            <p class="pr-5 text-sm font-semibold leading-snug text-slate-800 dark:text-slate-200">
                                {{ $product->product_name }}
                            </p>
                            <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-slate-400">Stok: {{ $product->stock }}</p>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============================================================
             RIGHT: Cart + Payment
             ============================================================ --}}
        <div class="flex flex-col gap-4 lg:col-span-2">

            {{-- Cart Card --}}
            <flux:card class="secondary-card flex flex-col gap-4 bg-[#E4FD97] border-[#c8e87d]">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Keranjang</flux:heading>
                    @if (! empty($cart))
                        <flux:button
                            wire:click="clearCart"
                            wire:confirm="Kosongkan keranjang?"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                        >
                            Kosongkan
                        </flux:button>
                    @endif
                </div>

                @if (empty($cart))
                    <div class="flex flex-col items-center gap-2 py-8 text-center">
                        <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <flux:text class="text-sm text-slate-400">Keranjang kosong.<br>Klik produk untuk menambahkan.</flux:text>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($cart as $productId => $item)
                            <div wire:key="cart-item-{{ $productId }}" class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">

                                {{-- Name + price --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ $item['name'] }}</p>
                                    <p class="text-xs text-slate-400">Rp {{ number_format($item['price'], 0, ',', '.') }} / pcs</p>
                                </div>

                                {{-- Qty controls --}}
                                <div class="flex items-center gap-1">
                                    <button
                                        wire:click="decrementQty({{ $productId }})"
                                        class="flex h-6 w-6 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800 transition-colors"
                                    >
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-6 text-center text-sm font-bold tabular-nums text-slate-800 dark:text-slate-200">{{ $item['qty'] }}</span>
                                    <button
                                        wire:click="incrementQty({{ $productId }})"
                                        class="flex h-6 w-6 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800 transition-colors"
                                    >
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Subtotal --}}
                                <p class="w-24 text-right text-sm font-semibold tabular-nums text-slate-800 dark:text-slate-200">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </p>

                                {{-- Remove --}}
                                <button
                                    wire:click="removeFromCart({{ $productId }})"
                                    class="text-slate-300 hover:text-red-400 transition-colors"
                                    title="Hapus"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            {{-- Payment Card --}}
            <flux:card class="secondary-card space-y-4 bg-[#E4FD97] border-[#c8e87d]">
                <flux:heading size="lg">Pembayaran</flux:heading>
                <flux:separator />

                {{-- Total --}}
                <div class="flex items-center justify-between">
                    <flux:text class="text-sm text-slate-500">Total</flux:text>
                    <span class="text-xl font-bold tabular-nums text-slate-900 dark:text-white">
                        Rp {{ number_format($this->total, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Amount Paid --}}
                <flux:field>
                    <flux:label>Uang Dibayar (Rp)</flux:label>
                    <flux:input
                        wire:model.live="amountPaid"
                        type="text"
                        placeholder="0"
                        inputmode="numeric"
                        :disabled="empty($cart)"
                    />
                </flux:field>

                {{-- Change --}}
                <div class="rounded-xl border p-4
                    {{ $this->change < 0 && $amountPaid !== ''
                        ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'
                        : ($this->change >= 0 && $amountPaid !== ''
                            ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20'
                            : 'border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50') }}">
                    <div class="flex items-center justify-between">
                        <flux:text class="text-sm font-medium
                            {{ $this->change < 0 && $amountPaid !== '' ? 'text-red-600 dark:text-red-400' : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $this->change < 0 && $amountPaid !== '' ? 'Kurang bayar' : 'Kembalian' }}
                        </flux:text>
                        <span class="text-xl font-bold tabular-nums
                            {{ $this->change < 0 && $amountPaid !== ''
                                ? 'text-red-600 dark:text-red-400'
                                : 'text-green-600 dark:text-green-400' }}">
                            @if ($amountPaid !== '')
                                Rp {{ number_format(abs($this->change), 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Stock / checkout error --}}
                @error('checkout')
                    <flux:callout variant="danger" icon="exclamation-triangle">
                        <flux:callout.text>{{ $message }}</flux:callout.text>
                    </flux:callout>
                @enderror

                {{-- Checkout button --}}
                <flux:button
                    wire:click="checkout"
                    wire:loading.attr="disabled"
                    variant="primary"
                    icon="shopping-bag"
                    class="w-full"
                    :disabled="empty($cart) || $this->change < 0 || $amountPaid === ''"
                >
                    <span wire:loading.remove wire:target="checkout">Proses Transaksi</span>
                    <span wire:loading wire:target="checkout">Menyimpan...</span>
                </flux:button>

                @if (empty($cart))
                    <flux:text class="text-center text-xs text-slate-400">Tambahkan produk ke keranjang untuk memulai.</flux:text>
                @elseif ($amountPaid === '')
                    <flux:text class="text-center text-xs text-slate-400">Masukkan jumlah uang yang dibayarkan.</flux:text>
                @elseif ($this->change < 0)
                    <flux:text class="text-center text-xs text-red-400">Uang dibayar kurang dari total.</flux:text>
                @endif
            </flux:card>

        </div>
        </div>{{-- end retail grid --}}
        </div>{{-- end tab retail --}}

        {{-- ================================================================
             TAB: TAGIHAN TIM
             ================================================================ --}}
        <div x-show="tab === 'tagihan'" class="mt-4">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- LEFT: Unpaid teams list --}}
                <div class="space-y-4">
                    <flux:input
                        wire:model.live.debounce.250ms="teamSearch"
                        placeholder="Cari nama tim..."
                        icon="magnifying-glass"
                    />

                    @if ($this->unpaidTeams->isEmpty())
                        <div class="flex flex-col items-center gap-3 rounded-2xl border border-dashed border-slate-200 py-16 dark:border-slate-700">
                            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <flux:text class="text-center text-slate-400">
                                {{ $teamSearch ? 'Tidak ada tim yang cocok.' : 'Semua tim sudah lunas.' }}
                            </flux:text>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($this->unpaidTeams as $team)
                                <button
                                    wire:click="selectTeam({{ $team->id }})"
                                    wire:key="team-bill-{{ $team->id }}"
                                    class="w-full rounded-xl border p-4 text-left transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50/50 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/10
                                        {{ $selectedTeamId === $team->id
                                            ? 'border-indigo-400 bg-indigo-50 dark:border-indigo-600 dark:bg-indigo-900/20'
                                            : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900' }}"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-amber-400 to-orange-500 text-xs font-bold text-white shadow-sm select-none">
                                                {{ strtoupper(substr($team->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $team->name }}</p>
                                                @if ($team->invoice_number)
                                                    <p class="font-mono text-xs text-slate-400">{{ $team->invoice_number }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <flux:badge color="red" size="sm">Belum Lunas</flux:badge>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Payment form for selected team --}}
                <div>
                    @if ($this->selectedTeam)
                        <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <flux:heading size="lg">{{ $this->selectedTeam->name }}</flux:heading>
                                    @if ($this->selectedTeam->invoice_number)
                                        <flux:text class="font-mono text-xs text-slate-400">
                                            {{ $this->selectedTeam->invoice_number }}
                                        </flux:text>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                        <span>{{ $this->selectedTeam->sport_type }}</span>
                                        @if ($this->selectedTeam->contact_person)
                                            <span>&middot; {{ $this->selectedTeam->contact_person }}</span>
                                        @endif
                                        @if ($this->selectedTeam->phone)
                                            <span>&middot; {{ $this->selectedTeam->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="deselectTeam" class="text-slate-300 hover:text-slate-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <flux:separator />

                            <flux:field>
                                <flux:label>Nominal Pembayaran (Rp)</flux:label>
                                <flux:input
                                    wire:model.live="teamPaymentAmount"
                                    type="text"
                                    placeholder="cth. 150000"
                                    inputmode="numeric"
                                    autofocus
                                />
                                <flux:description>
                                    Sesuai nominal biaya registrasi yang telah disepakati.
                                </flux:description>
                                <flux:error name="teamPaymentAmount" />
                            </flux:field>

                            <flux:button
                                wire:click="processTeamPayment"
                                wire:loading.attr="disabled"
                                variant="primary"
                                icon="check-circle"
                                class="w-full"
                                :disabled="! $teamPaymentAmount"
                            >
                                <span wire:loading.remove wire:target="processTeamPayment">Tandai Lunas</span>
                                <span wire:loading wire:target="processTeamPayment">Menyimpan...</span>
                            </flux:button>
                        </flux:card>
                    @else
                        <div class="flex h-full flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-200 p-12 dark:border-slate-700">
                            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                            <flux:text class="text-center text-sm text-slate-400">
                                Pilih tim di sebelah kiri<br>untuk memproses pembayaran.
                            </flux:text>
                        </div>
                    @endif
                </div>

            </div>
        </div>{{-- end tab tagihan --}}

        {{-- ================================================================
             TAB: DENDA TIM
             ================================================================ --}}
        <div x-show="tab === 'denda'" class="mt-4">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- LEFT: Approved teams list --}}
                <div class="space-y-4">
                    <flux:input
                        wire:model.live.debounce.250ms="dendaSearch"
                        placeholder="Cari nama tim..."
                        icon="magnifying-glass"
                    />

                    @if ($this->dendaTeams->isEmpty())
                        <div class="flex flex-col items-center gap-3 rounded-2xl border border-dashed border-slate-200 py-16 dark:border-slate-700">
                            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <flux:text class="text-center text-slate-400">
                                Tidak ada tim yang cocok.
                            </flux:text>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($this->dendaTeams as $team)
                                <button
                                    wire:click="selectDendaTeam({{ $team->id }})"
                                    wire:key="team-denda-{{ $team->id }}"
                                    class="w-full rounded-xl border p-4 text-left transition-all duration-200 hover:border-red-300 hover:bg-red-50/50 dark:hover:border-red-700 dark:hover:bg-red-900/10
                                        {{ $selectedDendaTeamId === $team->id
                                            ? 'border-red-400 bg-red-50 dark:border-red-600 dark:bg-red-900/20'
                                            : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900' }}"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-red-400 to-rose-600 text-xs font-bold text-white shadow-sm select-none">
                                                {{ strtoupper(substr($team->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $team->name }}</p>
                                                @if ($team->invoice_number)
                                                    <p class="font-mono text-xs text-slate-400">{{ $team->invoice_number }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <flux:badge color="red" size="sm">Denda</flux:badge>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Denda payment form for selected team --}}
                <div>
                    @if ($this->selectedDendaTeam)
                        <flux:card class="secondary-card space-y-5 bg-[#E4FD97] border-[#c8e87d]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <flux:heading size="lg">{{ $this->selectedDendaTeam->name }}</flux:heading>
                                    @if ($this->selectedDendaTeam->invoice_number)
                                        <flux:text class="font-mono text-xs text-slate-400">
                                            {{ $this->selectedDendaTeam->invoice_number }}
                                        </flux:text>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                        <span>{{ $this->selectedDendaTeam->sport_type }}</span>
                                        @if ($this->selectedDendaTeam->contact_person)
                                            <span>&middot; {{ $this->selectedDendaTeam->contact_person }}</span>
                                        @endif
                                        @if ($this->selectedDendaTeam->phone)
                                            <span>&middot; {{ $this->selectedDendaTeam->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="deselectDendaTeam" class="text-slate-300 hover:text-slate-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <flux:separator />

                            <flux:field>
                                <flux:label>Keterangan Denda</flux:label>
                                <flux:input
                                    wire:model.live="dendaNotes"
                                    type="text"
                                    placeholder="cth. Kartu merah, pelanggaran keras"
                                    maxlength="255"
                                    autofocus
                                />
                                <flux:description>
                                    Alasan atau jenis pelanggaran yang menyebabkan denda.
                                </flux:description>
                                <flux:error name="dendaNotes" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Nominal Denda (Rp)</flux:label>
                                <flux:input
                                    wire:model.live="dendaAmount"
                                    type="text"
                                    placeholder="cth. 50000"
                                    inputmode="numeric"
                                />
                                <flux:description>
                                    Nominal denda/pelanggaran tim yang harus dibayarkan.
                                </flux:description>
                                <flux:error name="dendaAmount" />
                            </flux:field>

                            <flux:button
                                wire:click="processDenda"
                                wire:loading.attr="disabled"
                                variant="primary"
                                icon="exclamation-circle"
                                class="w-full"
                                :disabled="! $dendaAmount || ! $dendaNotes"
                            >
                                <span wire:loading.remove wire:target="processDenda">Bayar Denda</span>
                                <span wire:loading wire:target="processDenda">Menyimpan...</span>
                            </flux:button>
                        </flux:card>
                    @else
                        <div class="flex h-full flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-200 p-12 dark:border-slate-700">
                            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                            <flux:text class="text-center text-sm text-slate-400">
                                Pilih tim di sebelah kiri<br>untuk memproses denda.
                            </flux:text>
                        </div>
                    @endif
                </div>

            </div>
        </div>{{-- end tab denda --}}

    </div>{{-- end x-data tab --}}

    {{-- Receipt Panel — shown after successful checkout --}}
    @if ($lastReceipt)
        <div class="mt-2">
            <flux:card class="space-y-5 border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-900/10">

                {{-- Receipt Header --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <flux:heading size="lg">Transaksi Berhasil</flux:heading>
                            <flux:text class="text-xs text-slate-500">
                                #{{ $lastReceipt['id'] }} &middot; Kasir: {{ $lastReceipt['cashier'] }}
                            </flux:text>
                        </div>
                    </div>
                    <flux:button wire:click="dismissReceipt" size="sm" variant="ghost" icon="x-mark">
                        Tutup
                    </flux:button>
                </div>

                <flux:separator />

                {{-- Receipt Items --}}
                <div class="space-y-2">
                    @foreach ($lastReceipt['items'] as $item)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-700 dark:text-slate-300">
                                {{ $item['name'] }}
                                <span class="text-slate-400">&times;{{ $item['qty'] }}</span>
                            </span>
                            <span class="tabular-nums font-medium text-slate-800 dark:text-slate-200">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <flux:separator />

                {{-- Receipt Totals --}}
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total</span>
                        <span class="tabular-nums font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($lastReceipt['total'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dibayar</span>
                        <span class="tabular-nums text-slate-700 dark:text-slate-300">
                            Rp {{ number_format($lastReceipt['paid'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between font-semibold">
                        <span class="text-green-700 dark:text-green-400">Kembalian</span>
                        <span class="tabular-nums text-green-700 dark:text-green-400">
                            Rp {{ number_format($lastReceipt['change'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

            </flux:card>
        </div>
    @endif

</div>
