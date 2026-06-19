<?php

use App\Livewire\Dashboard;

test('admin dashboard page can be rendered', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Widget akan ditambahkan di fase berikutnya');
});

test('admin dashboard page contains livewire component', function () {
    $this->get(route('admin.dashboard'))
        ->assertSeeLivewire(Dashboard::class);
});
