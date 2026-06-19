<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property int $played
 * @property int $win
 * @property int $draw
 * @property int $lose
 * @property int $points
 * @property int $goal_diff
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'team_id',
    'played',
    'win',
    'draw',
    'lose',
    'points',
    'goal_diff',
])]
class Standing extends Model
{
    /**
     * Get the team that belongs to the standing.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team_id');
    }
}
