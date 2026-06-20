<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use App\Models\TournamentTeam;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Generator Jadwal')]
class FixtureGenerator extends Component
{
    /** @var string 'round-robin'|'knockout' */
    public string $format = 'round-robin';

    /**
     * Approved teams available for fixture generation.
     *
     * @return Collection<int, TournamentTeam>
     */
    #[Computed]
    public function approvedTeams(): Collection
    {
        return TournamentTeam::where('status', 'approved')
            ->orderBy('name')
            ->get();
    }

    /**
     * Existing scheduled fixtures count to show current state.
     */
    #[Computed]
    public function existingFixturesCount(): int
    {
        return GameMatch::where('status', 'scheduled')->count();
    }

    /**
     * Generate Round-Robin fixtures for all approved teams.
     *
     * Each pair plays once. Rounds are labeled "Matchday N".
     * Uses a transaction so either all fixtures are saved or none.
     */
    public function generate(): void
    {
        $teams = $this->approvedTeams;

        if ($teams->count() < 2) {
            $this->addError(
                'generate',
                'Minimal 2 tim berstatus Approved diperlukan untuk membuat jadwal.'
            );

            return;
        }

        $teamIds = $teams->pluck('id')->toArray();

        $fixtures = match ($this->format) {
            'knockout' => $this->buildKnockoutFixtures($teamIds),
            default => $this->buildRoundRobinFixtures($teamIds),
        };

        DB::transaction(function () use ($fixtures): void {
            foreach ($fixtures as $fixture) {
                GameMatch::create($fixture);
            }
        });

        // Clear computed cache so counts refresh after generate
        unset($this->approvedTeams, $this->existingFixturesCount);

        $total = count($fixtures);
        $formatLabel = $this->format === 'knockout' ? 'Knockout (Babak 1)' : 'Round-Robin';
        Flux::toast(
            variant: 'success',
            text: "{$total} pertandingan {$formatLabel} berhasil digenerate."
        );
    }

    /**
     * Build Round-Robin fixture data for the given team IDs.
     *
     * Algorithm: for each unique pair (i, j) where i < j, assign to a
     * matchday. Matchday is incremented after every ceil(n/2) matches
     * so roughly n/2 matches per day (standard circle method grouping).
     *
     * @param  array<int, int>  $teamIds
     * @return array<int, array{team1_id: int, team2_id: int, round: string, status: string}>
     */
    private function buildRoundRobinFixtures(array $teamIds): array
    {
        $fixtures = [];
        $matchday = 1;
        $matchesInCurrentDay = 0;
        $teamsCount = count($teamIds);
        // With n teams, each matchday ideally has floor(n/2) matches
        $matchesPerDay = (int) max(1, floor($teamsCount / 2));

        for ($i = 0; $i < $teamsCount - 1; $i++) {
            for ($j = $i + 1; $j < $teamsCount; $j++) {
                $fixtures[] = [
                    'team1_id' => $teamIds[$i],
                    'team2_id' => $teamIds[$j],
                    'round' => "Matchday {$matchday}",
                    'status' => 'scheduled',
                ];

                $matchesInCurrentDay++;

                if ($matchesInCurrentDay >= $matchesPerDay) {
                    $matchday++;
                    $matchesInCurrentDay = 0;
                }
            }
        }

        return $fixtures;
    }

    /**
     * Build Knockout (single-elimination) fixtures for Round 1 only.
     *
     * Teams are shuffled randomly for seeding. If the count is odd,
     * the last team gets a BYE — recorded in notes with no opponent
     * (team1_id = team2_id = bye team, flagged via notes).
     * Subsequent rounds must be created manually after results are in.
     *
     * Round label is derived from the number of participants:
     *   2  → Final
     *   4  → Semifinal
     *   8  → Perempat Final
     *   16 → 16 Besar
     *   n  → Babak {n} Tim (fallback)
     *
     * @param  array<int, int>  $teamIds
     * @return array<int, array{team1_id: int, team2_id: int, round: string, status: string, notes: string|null}>
     */
    private function buildKnockoutFixtures(array $teamIds): array
    {
        shuffle($teamIds);

        $roundLabel = $this->knockoutRoundLabel(count($teamIds));
        $fixtures = [];
        $byeTeamId = null;

        // Handle odd number: last team gets a BYE
        if (count($teamIds) % 2 !== 0) {
            $byeTeamId = array_pop($teamIds);
        }

        // Pair remaining teams sequentially after shuffle
        $count = count($teamIds);
        for ($i = 0; $i < $count; $i += 2) {
            $fixtures[] = [
                'team1_id' => $teamIds[$i],
                'team2_id' => $teamIds[$i + 1],
                'round' => $roundLabel,
                'status' => 'scheduled',
                'notes' => null,
            ];
        }

        // BYE entry: team advances automatically, stored for bracket visibility
        if ($byeTeamId !== null) {
            $fixtures[] = [
                'team1_id' => $byeTeamId,
                'team2_id' => $byeTeamId,
                'round' => $roundLabel,
                'status' => 'done',
                'notes' => 'BYE — Tim ini otomatis lolos ke babak berikutnya.',
            ];
        }

        return $fixtures;
    }

    /**
     * Resolve a human-readable round name based on participant count.
     */
    private function knockoutRoundLabel(int $teamCount): string
    {
        return match (true) {
            $teamCount <= 2 => 'Final',
            $teamCount <= 4 => 'Semifinal',
            $teamCount <= 8 => 'Perempat Final',
            $teamCount <= 16 => '16 Besar',
            $teamCount <= 32 => '32 Besar',
            default => "Babak {$teamCount} Tim",
        };
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.fixture-generator');
    }
}
