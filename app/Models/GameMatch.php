<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team1_id
 * @property int $team2_id
 * @property string|null $round
 * @property Carbon|null $match_date
 * @property string|null $venue
 * @property int $score_team1
 * @property int $score_team2
 * @property int|null $winner_id
 * @property string|null $referee
 * @property string $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'team1_id',
    'team2_id',
    'round',
    'match_date',
    'venue',
    'score_team1',
    'score_team2',
    'winner_id',
    'referee',
    'status',
    'notes',
])]
class GameMatch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matches';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
        ];
    }

    /**
     * Get the team 1 of the match.
     */
    public function team1(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team1_id');
    }

    /**
     * Get the team 2 of the match.
     */
    public function team2(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team2_id');
    }

    /**
     * Get the winner of the match.
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'winner_id');
    }
}
