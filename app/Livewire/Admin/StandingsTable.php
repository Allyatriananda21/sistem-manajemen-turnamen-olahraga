<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use App\Models\Standing;
use App\Models\TournamentTeam;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Klasemen')]
class StandingsTable extends Component
{
    /**
     * Selected round/pool filter. Empty string means "all rounds".
     */
    public string $roundFilter = '';

    /** Filter cabang olahraga. Kosong = semua. */
    public string $sportFilter = '';

    /**
     * Available sport types from all teams that have standings.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function availableSports(): Collection
    {
        return TournamentTeam::whereIn(
            'id',
            Standing::select('team_id')
        )
            ->distinct()
            ->orderBy('sport_type')
            ->pluck('sport_type');
    }

    /**
     * Distinct round values found in finished matches, filtered by sport.
     * Used to populate the filter dropdown.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function availableRounds(): Collection
    {
        return GameMatch::where('status', 'done')
            ->whereColumn('team1_id', '!=', 'team2_id')
            ->whereNotNull('round')
            ->when(
                $this->sportFilter,
                fn ($q) => $q->whereHas(
                    'team1',
                    fn ($tq) => $tq->where('sport_type', $this->sportFilter),
                ),
            )
            ->distinct()
            ->orderBy('round')
            ->pluck('round');
    }

    /**
     * Standings ordered by points desc, then goal_diff desc.
     *
     * When a round filter is active, only teams that participated in
     * that round are shown (joined via the matches table).
     *
     * @return Collection<int, Standing>
     */
    #[Computed]
    public function standings(): Collection
    {
        $query = Standing::with('team')
            ->orderByDesc('points')
            ->orderByDesc('goal_diff')
            ->orderBy('win', 'desc');

        if ($this->sportFilter !== '') {
            // Restrict to teams with matching sport_type
            $sportTeamIds = TournamentTeam::where('sport_type', $this->sportFilter)
                ->pluck('id');
            $query->whereIn('team_id', $sportTeamIds);
        }

        if ($this->roundFilter !== '') {
            // Restrict to teams that played in the selected round
            $teamIds = GameMatch::where('status', 'done')
                ->where('round', $this->roundFilter)
                ->whereColumn('team1_id', '!=', 'team2_id')
                ->get(['team1_id', 'team2_id'])
                ->flatMap(fn ($m) => [$m->team1_id, $m->team2_id])
                ->unique()
                ->values();

            $query->whereIn('team_id', $teamIds);
        }

        return $query->get();
    }

    public function updatedRoundFilter(): void
    {
        unset($this->standings, $this->availableRounds);
    }

    public function updatedSportFilter(): void
    {
        $this->roundFilter = '';
        unset($this->standings, $this->availableRounds);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.standings-table');
    }
}
