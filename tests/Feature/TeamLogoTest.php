<?php

use App\Models\TournamentTeam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('team detail page renders logo with team-logos path correctly', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $team = TournamentTeam::create([
        'name' => 'Tim Garuda',
        'sport_type' => 'Futsal',
        'contact_person' => 'Ahmad',
        'phone' => '081234567800',
        'logo' => 'team-logos/garuda.jpg',
        'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.teams.show', $team));
    $response->assertOk();
    $response->assertSee('/storage/team-logos/garuda.jpg', false);
});

test('team detail page renders logo with public path correctly', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $team = TournamentTeam::create([
        'name' => 'Tim Elang',
        'sport_type' => 'Futsal',
        'contact_person' => 'Budi',
        'phone' => '081234567801',
        'logo' => 'public/team-logos/elang.jpg',
        'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.teams.show', $team));
    $response->assertOk();
    $response->assertSee('/storage/team-logos/elang.jpg', false);
});

test('team detail page renders logo with absolute url correctly', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $team = TournamentTeam::create([
        'name' => 'Tim Rajawali',
        'sport_type' => 'Futsal',
        'contact_person' => 'Candra',
        'phone' => '081234567802',
        'logo' => 'http://example.com/rajawali.jpg',
        'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.teams.show', $team));
    $response->assertOk();
    $response->assertSee('http://example.com/rajawali.jpg', false);
});

test('team detail page renders initials fallback when logo is null', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $team = TournamentTeam::create([
        'name' => 'Tim Merpati',
        'sport_type' => 'Futsal',
        'contact_person' => 'Dedi',
        'phone' => '081234567803',
        'logo' => null,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.teams.show', $team));
    $response->assertOk();
    $response->assertSee('TI'); // Initials of 'Tim Merpati' => 'TI'
});

test('team list page renders team logo and fallback initials', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $teamWithLogo = TournamentTeam::create([
        'name' => 'Garuda FC',
        'sport_type' => 'Futsal',
        'contact_person' => 'Ahmad',
        'phone' => '081234567800',
        'logo' => 'team-logos/garuda.jpg',
        'status' => 'approved',
    ]);

    $teamWithoutLogo = TournamentTeam::create([
        'name' => 'Merpati FC',
        'sport_type' => 'Futsal',
        'contact_person' => 'Dedi',
        'phone' => '081234567803',
        'logo' => null,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.teams'));
    $response->assertOk();
    $response->assertSee('/storage/team-logos/garuda.jpg', false);
    $response->assertSee('ME'); // Initials of 'Merpati FC' => 'ME'
});
