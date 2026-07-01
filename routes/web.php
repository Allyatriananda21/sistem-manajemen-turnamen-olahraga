<?php

use App\Livewire\Admin\BracketView;
use App\Livewire\Admin\FixtureGenerator;
use App\Livewire\Admin\GalleryManagement;
use App\Livewire\Admin\MatchControlPanel;
use App\Livewire\Admin\MatchList;
use App\Livewire\Admin\MatchStatisticsInput;
use App\Livewire\Admin\PlayerManagement;
use App\Livewire\Admin\PosCashier;
use App\Livewire\Admin\PosProductManagement;
use App\Livewire\Admin\Report;
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
        Route::get('admin/teams/{team}/players', PlayerManagement::class)
            ->middleware('role:admin,wasit')
            ->name('admin.teams.players');
        Route::get('admin/fixtures', FixtureGenerator::class)->name('admin.fixtures');
        Route::get('admin/bracket', BracketView::class)->name('admin.bracket');
        Route::get('admin/matches/{match}/statistics', MatchStatisticsInput::class)
            ->middleware('role:admin,wasit')
            ->name('admin.matches.statistics');
        Route::get('admin/gallery', GalleryManagement::class)->name('admin.gallery');
        Route::get('admin/laporan', Report::class)->name('admin.laporan');
    });
});

// Kelola Produk POS: diakses admin dan kasir (kasir read-only via view)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('admin/pos/products', PosProductManagement::class)->name('admin.pos.products');
});

// Pertandingan & Klasemen: diakses admin dan wasit
Route::middleware(['auth', 'role:admin,wasit'])->group(function () {
    Route::get('admin/matches', MatchList::class)->name('admin.matches');
    Route::get('admin/matches/{match}/control', MatchControlPanel::class)->name('admin.matches.control');
    Route::get('admin/standings', StandingsTable::class)->name('admin.standings');
});

// Kasir POS: diakses admin dan kasir
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('admin/pos', PosCashier::class)->name('admin.pos');
});

require __DIR__.'/settings.php';
