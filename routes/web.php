<?php

use App\Livewire\Admin\FixtureGenerator;
use App\Livewire\Admin\MatchControlPanel;
use App\Livewire\Admin\MatchList;
use App\Livewire\Admin\StandingsTable;
use App\Livewire\Admin\TeamDetail;
use App\Livewire\Admin\TeamList;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('admin/dashboard', Dashboard::class)->name('admin.dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('admin/users', UserManagement::class)->name('admin.users');
        Route::get('admin/teams', TeamList::class)->name('admin.teams');
        Route::get('admin/teams/{team}', TeamDetail::class)->name('admin.teams.show');
        Route::get('admin/fixtures', FixtureGenerator::class)->name('admin.fixtures');
        Route::get('admin/matches', MatchList::class)->name('admin.matches');
        Route::get('admin/matches/{match}/control', MatchControlPanel::class)
            ->middleware('role:admin,wasit')
            ->name('admin.matches.control');
        Route::get('admin/standings', StandingsTable::class)
            ->middleware('role:admin,wasit')
            ->name('admin.standings');
    });
});

require __DIR__.'/settings.php';
