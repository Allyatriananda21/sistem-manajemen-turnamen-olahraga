<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\Standing;
use Illuminate\Support\Facades\DB;

class StandingsCalculator
{
    /**
     * Rebuild the entire standings table from scratch.
     *
     * Steps:
     *   1. Reset all existing standing rows to zero.
     *   2. Load every non-BYE match with status 'done'.
     *   3. Accumulate played / win / draw / lose / points / goal_diff per team.
     *   4. Upsert all accumulated results in one transaction.
     *
     * This full-rebuild approach is intentionally simple and safe:
     * - Idempotent — can be called multiple times with the same result.
     * - Correct after score corrections on already-finished matches.
     * - Suitable for the expected data volume of a university tournament.
     */
    public function rebuild(): void
    {
        DB::transaction(function (): void {
            // Step 1: zero-out all current standing rows
            Standing::query()->update([
                'played' => 0,
                'win' => 0,
                'draw' => 0,
                'lose' => 0,
                'points' => 0,
                'goal_diff' => 0,
            ]);

            // Step 2: load all finished, real matches (skip BYE)
            $doneMatches = GameMatch::where('status', 'done')
                ->whereColumn('team1_id', '!=', 'team2_id')
                ->get(['team1_id', 'team2_id', 'score_team1', 'score_team2', 'winner_id']);

            if ($doneMatches->isEmpty()) {
                return;
            }

            // Step 3: accumulate stats per team in memory
            /** @var array<int, array{played:int,win:int,draw:int,lose:int,points:int,goal_diff:int}> */
            $totals = [];

            foreach ($doneMatches as $match) {
                $this->accumulateForTeam($totals, $match->team1_id, $match->score_team1, $match->score_team2, $match->winner_id);
                $this->accumulateForTeam($totals, $match->team2_id, $match->score_team2, $match->score_team1, $match->winner_id);
            }

            // Step 4: upsert — create the row if absent, update if present
            foreach ($totals as $teamId => $stats) {
                Standing::updateOrCreate(
                    ['team_id' => $teamId],
                    $stats,
                );
            }
        });
    }

    /**
     * Accumulate one match result into the in-memory totals array.
     *
     * @param  array<int, array{played:int,win:int,draw:int,lose:int,points:int,goal_diff:int}>  $totals
     */
    private function accumulateForTeam(
        array &$totals,
        int $teamId,
        int $goalsFor,
        int $goalsAgainst,
        ?int $winnerId,
    ): void {
        if (! isset($totals[$teamId])) {
            $totals[$teamId] = [
                'played' => 0,
                'win' => 0,
                'draw' => 0,
                'lose' => 0,
                'points' => 0,
                'goal_diff' => 0,
            ];
        }

        $isDraw = $winnerId === null;
        $isWin = $winnerId === $teamId;

        $totals[$teamId]['played']++;
        $totals[$teamId]['goal_diff'] += $goalsFor - $goalsAgainst;

        if ($isWin) {
            $totals[$teamId]['win']++;
            $totals[$teamId]['points'] += 3;
        } elseif ($isDraw) {
            $totals[$teamId]['draw']++;
            $totals[$teamId]['points'] += 1;
        } else {
            $totals[$teamId]['lose']++;
        }
    }
}
