<?php

namespace App\Livewire;

use App\Models\GameMatch;
use App\Models\PosTransaction;
use App\Models\TournamentTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    // -----------------------------------------------------------------------
    // Filter state — drives all computed properties below
    // -----------------------------------------------------------------------

    public string $filterDateFrom = '';

    public string $filterDateTo = '';

    public string $filterRound = '';

    public int|string $filterTeamId = '';

    /**
     * Reset all filters to their default (empty) state.
     */
    public function resetFilters(): void
    {
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterRound = '';
        $this->filterTeamId = '';

        // Invalidate all computed caches
        unset(
            $this->revenueChartData,
            $this->totalTeams,
            $this->totalApprovedTeams,
            $this->uniqueRounds,
            $this->competitionStatus,
            $this->totalMatches,
            $this->matchStatusBreakdown,
        );
    }

    // -----------------------------------------------------------------------
    // Filter options (never affected by active filters themselves)
    // -----------------------------------------------------------------------

    /**
     * All distinct rounds for the Round dropdown.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function allRounds(): Collection
    {
        return GameMatch::whereNotNull('round')
            ->distinct()
            ->orderBy('round')
            ->pluck('round');
    }

    /**
     * All approved teams for the Competitor dropdown.
     *
     * @return Collection<int, TournamentTeam>
     */
    #[Computed]
    public function allApprovedTeams(): Collection
    {
        return TournamentTeam::where('status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // -----------------------------------------------------------------------
    // Shared match query scope helper
    // -----------------------------------------------------------------------

    /**
     * Base match query with all active filters applied.
     *
     * @return Builder<GameMatch>
     */
    private function filteredMatchQuery()
    {
        return GameMatch::query()
            ->when(
                $this->filterDateFrom,
                fn ($q) => $q->whereDate('match_date', '>=', $this->filterDateFrom)
            )
            ->when(
                $this->filterDateTo,
                fn ($q) => $q->whereDate('match_date', '<=', $this->filterDateTo)
            )
            ->when(
                $this->filterRound,
                fn ($q) => $q->where('round', $this->filterRound)
            )
            ->when(
                $this->filterTeamId !== '',
                fn ($q) => $q->where(function ($q2) {
                    $q2->where('team1_id', $this->filterTeamId)
                        ->orWhere('team2_id', $this->filterTeamId);
                })
            );
    }

    /**
     * Base transaction query with date filter applied.
     *
     * @return Builder<PosTransaction>
     */
    private function filteredTransactionQuery()
    {
        return PosTransaction::query()
            ->when(
                $this->filterDateFrom,
                fn ($q) => $q->whereDate('created_at', '>=', $this->filterDateFrom)
            )
            ->when(
                $this->filterDateTo,
                fn ($q) => $q->whereDate('created_at', '<=', $this->filterDateTo)
            );
    }

    // -----------------------------------------------------------------------
    // Revenue Chart
    // -----------------------------------------------------------------------

    /**
     * Revenue data grouped by date and transaction_type for Chart.js.
     *
     * @return array{labels: list<string>, datasets: array{registrasi: list<float>, retail: list<float>, denda: list<float>}}
     */
    #[Computed]
    public function revenueChartData(): array
    {
        $rows = $this->filteredTransactionQuery()
            ->selectRaw('DATE(created_at) as date, transaction_type, SUM(total_amount) as total')
            ->groupBy('date', 'transaction_type')
            ->orderBy('date')
            ->get();

        if ($rows->isEmpty()) {
            return ['labels' => [], 'datasets' => ['registrasi' => [], 'retail' => [], 'denda' => []]];
        }

        $dates = $rows->pluck('date')->unique()->sort()->values()->toArray();

        $lookup = [];
        foreach ($rows as $row) {
            $lookup[$row->date][$row->transaction_type] = (float) $row->total;
        }

        $types = ['registrasi', 'retail', 'denda'];
        $datasets = [];
        foreach ($types as $type) {
            $datasets[$type] = array_map(
                fn ($date) => $lookup[$date][$type] ?? 0,
                $dates,
            );
        }

        return ['labels' => $dates, 'datasets' => $datasets];
    }

    // -----------------------------------------------------------------------
    // Competition Overview
    // -----------------------------------------------------------------------

    /**
     * Total registered teams (all statuses). Not affected by match filters.
     */
    #[Computed]
    public function totalTeams(): int
    {
        return TournamentTeam::count();
    }

    /**
     * Number of approved teams.
     */
    #[Computed]
    public function totalApprovedTeams(): int
    {
        return TournamentTeam::where('status', 'approved')->count();
    }

    /**
     * Distinct round names visible under current filters.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function uniqueRounds(): Collection
    {
        return $this->filteredMatchQuery()
            ->whereNotNull('round')
            ->distinct()
            ->orderBy('round')
            ->pluck('round');
    }

    /**
     * High-level competition status derived from filtered matches.
     */
    #[Computed]
    public function competitionStatus(): string
    {
        if ($this->totalMatches === 0) {
            return 'Belum Mulai';
        }

        $breakdown = $this->matchStatusBreakdown;

        if ($breakdown['scheduled'] > 0 || $breakdown['ongoing'] > 0) {
            return 'In-Play';
        }

        return 'Selesai';
    }

    // -----------------------------------------------------------------------
    // Fixtures Summary
    // -----------------------------------------------------------------------

    /**
     * Total match count under current filters.
     */
    #[Computed]
    public function totalMatches(): int
    {
        return $this->filteredMatchQuery()->count();
    }

    /**
     * Match counts broken down by status under current filters.
     *
     * @return array{scheduled: int, ongoing: int, done: int, cancelled: int}
     */
    #[Computed]
    public function matchStatusBreakdown(): array
    {
        $counts = $this->filteredMatchQuery()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'scheduled' => $counts['scheduled'] ?? 0,
            'ongoing' => $counts['ongoing'] ?? 0,
            'done' => $counts['done'] ?? 0,
            'cancelled' => $counts['cancelled'] ?? 0,
        ];
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.dashboard');
    }
}
