<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Bracket Visual')]
class BracketView extends Component
{
    /**
     * Round ordering — early rounds first, Final last.
     *
     * @var array<string, int>
     */
    private const ROUND_ORDER = [
        '32 Besar'       => 10,
        '16 Besar'       => 20,
        'Perempat Final' => 30,
        'Semifinal'      => 40,
        'Final'          => 50,
    ];

    // Modal state
    public bool $showMatchModal = false;

    public ?int $selectedMatchId = null;

    /**
     * Knockout matches grouped and sorted by round.
     *
     * @return Collection<int, array{round: string, matches: Collection<int, GameMatch>}>
     */
    #[Computed]
    public function rounds(): Collection
    {
        $matches = GameMatch::with(['team1:id,name', 'team2:id,name', 'winner:id,name'])
            ->whereNotNull('round')
            ->where('round', 'not like', 'Matchday%')
            ->orderBy('id')
            ->get();

        return $matches
            ->groupBy('round')
            ->sortBy(fn ($_, $round) => self::ROUND_ORDER[$round] ?? 99)
            ->map(fn ($roundMatches, $round) => [
                'round'   => $round,
                'matches' => $roundMatches,
            ])
            ->values();
    }

    /**
     * The selected match for the detail modal.
     */
    #[Computed]
    public function selectedMatch(): ?GameMatch
    {
        if (! $this->selectedMatchId) {
            return null;
        }

        return GameMatch::with(['team1', 'team2', 'winner'])->find($this->selectedMatchId);
    }

    /**
     * Open the detail modal for the given match.
     */
    public function openMatch(int $matchId): void
    {
        $this->selectedMatchId = $matchId;
        $this->showMatchModal  = true;
        unset($this->selectedMatch);
    }

    /**
     * Close the detail modal.
     */
    public function closeModal(): void
    {
        $this->showMatchModal  = false;
        $this->selectedMatchId = null;
        unset($this->selectedMatch);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.bracket-view');
    }
}
