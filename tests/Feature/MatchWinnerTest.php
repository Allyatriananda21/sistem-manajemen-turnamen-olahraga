<?php

use App\Models\GameMatch;
use App\Models\TournamentTeam;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Create two teams and a match between them in 'ongoing' state.
 *
 * @return array{team1: TournamentTeam, team2: TournamentTeam, match: GameMatch}
 */
function makeOngoingMatch(int $score1 = 0, int $score2 = 0): array
{
    $team1 = TournamentTeam::create([
        'name' => 'Tim Alpha', 'sport_type' => 'Futsal',
        'contact_person' => 'A', 'phone' => '081',
        'status' => 'approved', 'payment_status' => 'paid',
    ]);

    $team2 = TournamentTeam::create([
        'name' => 'Tim Beta', 'sport_type' => 'Futsal',
        'contact_person' => 'B', 'phone' => '082',
        'status' => 'approved', 'payment_status' => 'paid',
    ]);

    $match = GameMatch::create([
        'team1_id'    => $team1->id,
        'team2_id'    => $team2->id,
        'round'       => 'Final',
        'status'      => 'ongoing',
        'score_team1' => $score1,
        'score_team2' => $score2,
    ]);

    return ['team1' => $team1, 'team2' => $team2, 'match' => $match];
}

// ---------------------------------------------------------------------------
// Winner determination
// ---------------------------------------------------------------------------

test('team1 wins when score_team1 is higher', function () {
    $admin    = User::factory()->create(['role' => 'admin']);
    $data     = makeOngoingMatch(score1: 3, score2: 1);
    $match    = $data['match'];

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\MatchControlPanel::class, ['match' => $match])
        ->call('advance'); // ongoing → done

    $match->refresh();

    expect($match->status)->toBe('done');
    expect($match->winner_id)->toBe($data['team1']->id);
});

test('team2 wins when score_team2 is higher', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $data  = makeOngoingMatch(score1: 0, score2: 2);
    $match = $data['match'];

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\MatchControlPanel::class, ['match' => $match])
        ->call('advance');

    $match->refresh();

    expect($match->status)->toBe('done');
    expect($match->winner_id)->toBe($data['team2']->id);
});

test('winner_id is null when scores are equal (draw)', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $data  = makeOngoingMatch(score1: 2, score2: 2);
    $match = $data['match'];

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\MatchControlPanel::class, ['match' => $match])
        ->call('advance');

    $match->refresh();

    expect($match->status)->toBe('done');
    expect($match->winner_id)->toBeNull();
});

test('zero-zero match requires confirmation before finalizing', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $data  = makeOngoingMatch(score1: 0, score2: 0);
    $match = $data['match'];

    $component = Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\MatchControlPanel::class, ['match' => $match])
        ->call('advance');

    // Should open confirmation modal, NOT finalize yet
    $component->assertSet('showZeroScoreConfirm', true);

    $match->refresh();
    expect($match->status)->toBe('ongoing'); // not yet done

    // Confirm → now finalizes
    $component->call('confirmFinishZeroScore');
    $match->refresh();

    expect($match->status)->toBe('done');
    expect($match->winner_id)->toBeNull(); // 0-0 is a draw
});

test('status cannot jump from scheduled directly to done', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $team1 = TournamentTeam::create(['name' => 'T1', 'sport_type' => 'F', 'contact_person' => 'x', 'phone' => '1', 'status' => 'approved', 'payment_status' => 'paid']);
    $team2 = TournamentTeam::create(['name' => 'T2', 'sport_type' => 'F', 'contact_person' => 'y', 'phone' => '2', 'status' => 'approved', 'payment_status' => 'paid']);

    $match = GameMatch::create([
        'team1_id' => $team1->id, 'team2_id' => $team2->id,
        'status' => 'scheduled', 'score_team1' => 3, 'score_team2' => 0,
    ]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\MatchControlPanel::class, ['match' => $match])
        ->call('advance'); // scheduled → ongoing (not done)

    $match->refresh();

    expect($match->status)->toBe('ongoing');
    expect($match->winner_id)->toBeNull();
});
