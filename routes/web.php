<?php

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
    });
});

require __DIR__.'/settings.php';
