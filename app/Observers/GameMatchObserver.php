<?php

namespace App\Observers;

use App\Models\GameMatch;
use App\Services\StandingsCalculator;

class GameMatchObserver
{
    public function __construct(
        private readonly StandingsCalculator $calculator,
    ) {}

    /**
     * Handle the GameMatch "updated" event.
     *
     * Triggers a full standings rebuild when:
     *   (a) status changes to 'done'          — new match finished
     *   (b) scores change on an already-done match — score correction by admin
     *
     * BYE matches (team1_id === team2_id) never affect standings and are skipped
     * early to avoid unnecessary work.
     */
    public function updated(GameMatch $match): void
    {
        if ($match->team1_id === $match->team2_id) {
            return;
        }

        $statusJustDone = $match->wasChanged('status') && $match->status === 'done';
        $scoreChangedOnDone = $match->status === 'done'
            && $match->wasChanged(['score_team1', 'score_team2']);

        if (! $statusJustDone && ! $scoreChangedOnDone) {
            return;
        }

        $this->calculator->rebuild();
    }
}
