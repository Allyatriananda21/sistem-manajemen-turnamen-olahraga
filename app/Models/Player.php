<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $team_id
 * @property string $full_name
 * @property int|null $jersey_number
 * @property string|null $position
 */
#[Fillable([
    'team_id',
    'full_name',
    'jersey_number',
    'position',
])]
class Player extends Model
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team_id');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(MatchStatistic::class, 'player_id');
    }
}
