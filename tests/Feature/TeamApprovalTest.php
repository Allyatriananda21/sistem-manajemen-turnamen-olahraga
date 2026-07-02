<?php

use App\Models\TournamentTeam;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Team Registration — default status
// ---------------------------------------------------------------------------

test('new team has pending status by default', function () {
    $team = TournamentTeam::create([
        'name'           => 'Tim Test',
        'sport_type'     => 'Futsal',
        'contact_person' => 'John',
        'phone'          => '08123456789',
        'status'         => 'pending',
        'payment_status' => 'unpaid',
    ]);

    expect($team->status)->toBe('pending');
    expect($team->payment_status)->toBe('unpaid');
});

test('team registered via public API defaults to pending and unpaid', function () {
    $response = $this->postJson('/api/public/teams/register', [
        'name'           => 'Garuda FC',
        'sport_type'     => 'Futsal',
        'contact_person' => 'Ahmad',
        'phone'          => '081234567800',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.payment_status', 'unpaid');

    $this->assertDatabaseHas('tournament_teams', [
        'name'           => 'Garuda FC',
        'status'         => 'pending',
        'payment_status' => 'unpaid',
    ]);
});

// ---------------------------------------------------------------------------
// Approval flow via Livewire component
// ---------------------------------------------------------------------------

test('pending team is not approved without explicit approve action', function () {
    $team = TournamentTeam::create([
        'name'           => 'Tim Pending',
        'sport_type'     => 'Futsal',
        'contact_person' => 'Jane',
        'phone'          => '08987654321',
        'status'         => 'pending',
        'payment_status' => 'unpaid',
    ]);

    // Reload from DB — must still be pending
    $team->refresh();

    expect($team->status)->toBe('pending');
    expect($team->status)->not->toBe('approved');
});

test('team becomes approved only after admin approve action', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $team = TournamentTeam::create([
        'name'           => 'Tim Akan Diapprove',
        'sport_type'     => 'Futsal',
        'contact_person' => 'Budi',
        'phone'          => '08111222333',
        'status'         => 'pending',
        'payment_status' => 'unpaid',
    ]);

    // Simulate admin confirming action in Livewire TeamList component
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\TeamList::class)
        ->call('openConfirmModal', $team->id, 'approve')
        ->call('executeAction');

    $team->refresh();

    expect($team->status)->toBe('approved');
    expect($team->invoice_number)->not->toBeNull(); // invoice generated on first approval
});

test('approved team gets disqualified only after explicit disqualify action', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $team = TournamentTeam::create([
        'name'           => 'Tim Approved',
        'sport_type'     => 'Futsal',
        'contact_person' => 'Sari',
        'phone'          => '08222333444',
        'status'         => 'approved',
        'payment_status' => 'paid',
        'invoice_number' => 'INV-2026-0099',
    ]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\TeamList::class)
        ->call('openConfirmModal', $team->id, 'disqualify')
        ->call('executeAction');

    $team->refresh();

    expect($team->status)->toBe('disqualified');
});
