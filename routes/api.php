<?php

use App\Http\Controllers\Api\PublicBracketController;
use App\Http\Controllers\Api\PublicMatchController;
use App\Http\Controllers\Api\PublicStandingController;
use App\Http\Controllers\Api\PublicTeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes (no authentication required)
|--------------------------------------------------------------------------
|
| These endpoints expose read-only data for the React.js landing page.
| All routes are prefixed with /api automatically by the framework.
|
*/

Route::prefix('public')->name('api.public.')->group(function () {
    Route::get('teams', [PublicTeamController::class, 'index'])->name('teams.index');
    Route::post('teams/register', [PublicTeamController::class, 'store'])->name('teams.register');
    Route::get('matches', [PublicMatchController::class, 'index'])->name('matches.index');
    Route::get('standings', [PublicStandingController::class, 'index'])->name('standings.index');
    Route::get('bracket', [PublicBracketController::class, 'index'])->name('bracket.index');
});
