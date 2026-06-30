<?php

use App\Livewire\Admin\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can view user management page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->get(route('admin.users'))
        ->assertOk();
});

test('non-admin cannot access user management page', function () {
    $wasit = User::factory()->create(['role' => 'wasit', 'is_active' => true]);

    $this->actingAs($wasit)
        ->get(route('admin.users'))
        ->assertForbidden();
});

test('guest is redirected to login from user management page', function () {
    $this->get(route('admin.users'))
        ->assertRedirect(route('login'));
});

test('admin can create a new user', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@contoh.com')
        ->set('password', 'password123')
        ->set('role', 'wasit')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'budi@contoh.com',
        'role' => 'wasit',
    ]);
});

test('store validates required fields', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', '')
        ->set('email', 'bukan-email')
        ->set('password', 'short')
        ->set('role', 'invalid')
        ->call('store')
        ->assertHasErrors(['name', 'email', 'password', 'role']);
});

test('store validates email uniqueness', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $existing = User::factory()->create(['email' => 'existing@contoh.com']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Test User')
        ->set('email', 'existing@contoh.com')
        ->set('password', 'password123')
        ->set('role', 'kasir')
        ->call('store')
        ->assertHasErrors(['email']);
});

test('admin can toggle user active status', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $target = User::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('toggleActive', $target->id);

    expect($target->fresh()->is_active)->toBeFalse();
});

test('admin cannot deactivate own account', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('toggleActive', $admin->id);

    expect($admin->fresh()->is_active)->toBeTrue();
});

test('admin can update user role', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $target = User::factory()->create(['role' => 'wasit']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('updateRole', $target->id, 'kasir');

    expect($target->fresh()->role)->toBe('kasir');
});

test('admin cannot change own role', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('updateRole', $admin->id, 'kasir');

    expect($admin->fresh()->role)->toBe('admin');
});

test('user list filters by search query', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'name' => 'Admin Super']);
    User::factory()->create(['name' => 'Budi Wasit', 'email' => 'budi@test.com']);
    User::factory()->create(['name' => 'Siti Kasir', 'email' => 'siti@test.com']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('search', 'Budi')
        ->assertSee('Budi Wasit')
        ->assertDontSee('Siti Kasir');
});
