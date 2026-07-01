<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <flux:heading size="xl">Galeri Foto</flux:heading>
        <flux:text class="mt-1 text-slate-500">Unggah dan kelola foto dokumentasi turnamen.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ============================================================
             LEFT: Upload Form
             ============================================================ --}}
        <div class="lg:col-span-1">
            <flux:card class="space-y-5">
                <flux:heading size="lg">Unggah Foto</flux:heading>
                <flux:separator />

                <form wire:submit="savePhoto" class="space-y-4">

                    {{-- File input --}}
                    <flux:field>
                        <flux:label>Foto <span class="text-red-500">*</span></flux:label>
                        <input
                            type="file"
                            wire:model="photo"
                            accept="image/*"
                            class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 focus:outline-none"
                        />
                        <flux:description>Maks. 4 MB. Format: JPG, PNG, WEBP.</flux:description>
                        <flux:error name="photo" />

                        {{-- Preview --}}
                        @if ($photo)
                            <div class="mt-2 overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-40 w-full object-cover" />
                            </div>
                        @endif
                    </flux:field>

                    {{-- Caption --}}
                    <flux:field>
                        <flux:label>Keterangan <span class="text-slate-400 font-normal">(opsional)</span></flux:label>
                        <flux:input
                            wire:model="caption"
                            type="text"
                            placeholder="cth. Selebrasi gol menit 89"
                            maxlength="255"
                        />
                    </flux:field>

                    {{-- Match association --}}
                    <flux:field>
                        <flux:label>Pertandingan Terkait <span class="text-slate-400 font-normal">(opsional)</span></flux:label>
                        <select
                            wire:model="matchId"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                        >
                            <option value="">— Foto Umum —</option>
                            @foreach ($this->doneMatches as $match)
                                <option value="{{ $match->id }}">
                                    {{ $match->team1->name }} vs {{ $match->team2->name }}
                                    @if ($match->round) ({{ $match->round }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <flux:description>Hubungkan foto ke pertandingan yang sudah selesai.</flux:description>
                    </flux:field>

                    <flux:button
                        type="submit"
                        variant="primary"
                        icon="arrow-up-tray"
                        class="w-full"
                        wire:loading.attr="disabled"
                        wire:target="photo,savePhoto"
                    >
                        <span wire:loading.remove wire:target="savePhoto">Unggah Foto</span>
                        <span wire:loading wire:target="savePhoto">Mengunggah...</span>
                    </flux:button>
                </form>

                {{-- Upload progress --}}
                <div wire:loading wire:target="photo" class="text-center text-xs text-slate-400">
                    Memproses file...
                </div>
            </flux:card>
        </div>

        {{-- ============================================================
             RIGHT: Photo Grid
             ============================================================ --}}
        <div class="lg:col-span-2 space-y-4">

            @if ($photos->isEmpty())
                <flux:card class="py-16">
                    <div class="flex flex-col items-center gap-3 text-center">
                        <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <flux:text class="text-slate-400">Belum ada foto. Unggah foto pertama.</flux:text>
                    </div>
                </flux:card>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($photos as $photo)
                        <div wire:key="photo-{{ $photo->id }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">

                            {{-- Image --}}
                            <img
                                src="{{ asset('storage/' . $photo->image_path) }}"
                                alt="{{ $photo->caption ?? 'Foto galeri' }}"
                                class="h-40 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                loading="lazy"
                            />

                            {{-- Overlay on hover --}}
                            <div class="absolute inset-0 flex flex-col justify-between bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100 p-3">
                                <div class="flex justify-end">
                                    <button
                                        wire:click="openDelete({{ $photo->id }})"
                                        class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500/90 text-white hover:bg-red-600 transition-colors"
                                        title="Hapus foto"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <div>
                                    @if ($photo->caption)
                                        <p class="text-xs font-medium text-white line-clamp-2">{{ $photo->caption }}</p>
                                    @endif
                                    @if ($photo->match_id && $photo->match)
                                        <p class="mt-0.5 text-[10px] text-white/70">
                                            {{ $photo->match->team1->name ?? '' }} vs {{ $photo->match->team2->name ?? '' }}
                                        </p>
                                    @endif
                                    <p class="mt-0.5 text-[10px] text-white/50">{{ $photo->uploaded_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($photos->hasPages())
                    <div class="pt-2">{{ $photos->links() }}</div>
                @endif

                <flux:text class="text-center text-xs text-slate-400">
                    {{ $photos->total() }} foto · {{ $photos->firstItem() }}–{{ $photos->lastItem() }} ditampilkan
                </flux:text>
            @endif

        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model.self="showDeleteModal" class="md:w-[24rem]" :dismissible="false">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Hapus Foto?</flux:heading>
                <flux:text class="mt-1 text-slate-500">
                    Foto akan dihapus dari server secara permanen dan tidak dapat dikembalikan.
                </flux:text>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">Batal</flux:button>
                <flux:button wire:click="destroy" variant="danger" icon="trash">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

</div>
