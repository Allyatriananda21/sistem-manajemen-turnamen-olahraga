<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use App\Models\Standing;
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

    /**
     * Distinct round values found in finished matches.
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
        // Clear computed cache so standings re-query with the new filter
        unset($this->standings);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.standings-table');
    }
}
