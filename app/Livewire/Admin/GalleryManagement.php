<?php

namespace App\Livewire\Admin;

use App\Models\Gallery;
use App\Models\GameMatch;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Galeri Foto')]
class GalleryManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Upload form fields
    #[Validate(['required', 'image', 'max:4096'])] // 4 MB
    public $photo = null;

    public string $caption = '';

    public string $matchId = '';

    // Delete modal state
    public bool $showDeleteModal = false;

    public ?int $deletingPhotoId = null;

    // -----------------------------------------------------------------------
    // Computed
    // -----------------------------------------------------------------------

    /**
     * Matches with status 'done' for the optional association dropdown.
     *
     * @return Collection<int, GameMatch>
     */
    #[Computed]
    public function doneMatches(): Collection
    {
        return GameMatch::with(['team1:id,name', 'team2:id,name'])
            ->where('status', 'done')
            ->whereColumn('team1_id', '!=', 'team2_id') // exclude BYE entries
            ->orderByDesc('match_date')
            ->get();
    }

    // -----------------------------------------------------------------------
    // Upload
    // -----------------------------------------------------------------------

    public function savePhoto(): void
    {
        $this->validate([
            'photo' => ['required', 'image', 'max:4096'],
        ]);

        $path = $this->photo->store('gallery', 'public');

        Gallery::create([
            'match_id' => $this->matchId ?: null,
            'image_path' => $path,
            'caption' => $this->caption ?: null,
        ]);

        $this->reset(['photo', 'caption', 'matchId']);
        $this->resetPage();

        Flux::toast(variant: 'success', text: 'Foto berhasil diunggah.');
    }

    // -----------------------------------------------------------------------
    // Delete
    // -----------------------------------------------------------------------

    public function openDelete(int $photoId): void
    {
        $this->deletingPhotoId = $photoId;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        $photo = Gallery::findOrFail($this->deletingPhotoId);

        // Remove file from storage
        if (Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $photo->delete();

        $this->showDeleteModal = false;
        $this->deletingPhotoId = null;

        Flux::toast(variant: 'danger', text: 'Foto berhasil dihapus.');
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render()
    {
        $photos = Gallery::with('match.team1:id,name', 'match.team2:id,name')
            ->orderByDesc('uploaded_at')
            ->paginate(12);

        return view('livewire.admin.gallery-management', compact('photos'));
    }
}
