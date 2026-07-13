<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use App\Models\TournamentTeam;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Daftar Pertandingan')]
class MatchList extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var string 'all'|'scheduled'|'ongoing'|'done'|'cancelled' */
    public string $statusFilter = 'all';

    /** Filter cabang olahraga. Kosong = semua. */
    public string $sportFilter = '';

    // Edit modal state
    public bool $showEditModal = false;

    public ?int $editMatchId = null;

    public string $editVenue = '';

    public string $editMatchDate = '';

    public string $editReferee = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSportFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Available sport types from all teams involved in matches.
     *
     * @return Collection<int, string>
     */
    public function getAvailableSportsProperty(): Collection
    {
        return TournamentTeam::whereIn(
            'id',
            GameMatch::select('team1_id')->union(GameMatch::select('team2_id'))
        )
            ->distinct()
            ->orderBy('sport_type')
            ->pluck('sport_type');
    }

    /**
     * Open the edit modal pre-filled with the given match's details.
     */
    public function openEdit(int $matchId): void
    {
        $match = GameMatch::findOrFail($matchId);

        $this->editMatchId = $matchId;
        $this->editVenue = $match->venue ?? '';
        $this->editMatchDate = $match->match_date?->format('Y-m-d\TH:i') ?? '';
        $this->editReferee = $match->referee ?? '';
        $this->resetValidation();
        $this->showEditModal = true;
    }

    /**
     * Persist venue, match_date, and referee changes for the selected match.
     */
    public function saveEdit(): void
    {
        $validated = $this->validate([
            'editVenue' => ['nullable', 'string', 'max:200'],
            'editMatchDate' => ['nullable', 'date'],
            'editReferee' => ['nullable', 'string', 'max:100'],
        ], [
            'editMatchDate.date' => 'Format tanggal tidak valid.',
            'editVenue.max' => 'Nama venue maksimal 200 karakter.',
            'editReferee.max' => 'Nama wasit maksimal 100 karakter.',
        ]);

        $match = GameMatch::findOrFail($this->editMatchId);

        $match->update([
            'venue' => $validated['editVenue'] ?: null,
            'match_date' => $validated['editMatchDate'] ?: null,
            'referee' => $validated['editReferee'] ?: null,
        ]);

        $this->showEditModal = false;
        $this->resetEditState();

        Flux::toast(variant: 'success', text: 'Detail pertandingan berhasil diperbarui.');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $matches = GameMatch::with(['team1', 'team2'])
            ->when(
                $this->search,
                fn ($query) => $query->where(function ($q) {
                    $q->whereHas('team1', fn ($tq) => $tq->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('team2', fn ($tq) => $tq->where('name', 'like', "%{$this->search}%"));
                })
            )
            ->when(
                $this->statusFilter !== 'all',
                fn ($query) => $query->where('status', $this->statusFilter)
            )
            ->when(
                $this->sportFilter,
                fn ($query) => $query->whereHas(
                    'team1',
                    fn ($tq) => $tq->where('sport_type', $this->sportFilter),
                )
            )
            ->orderByRaw("FIELD(status, 'ongoing', 'scheduled', 'done', 'cancelled')")
            ->orderBy('match_date')
            ->paginate(15);

        return view('livewire.admin.match-list', [
            'matches' => $matches,
            'availableSports' => $this->getAvailableSportsProperty(),
        ]);
    }

    private function resetEditState(): void
    {
        $this->editMatchId = null;
        $this->editVenue = '';
        $this->editMatchDate = '';
        $this->editReferee = '';
    }
}
