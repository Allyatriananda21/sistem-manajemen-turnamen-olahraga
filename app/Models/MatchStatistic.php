<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $match_id
 * @property int $player_id
 * @property string $stat_type goal|yellow_card|red_card|assist|mvp
 * @property int|null $minute
 */
#[Fillable([
    'match_id',
    'player_id',
    'stat_type',
    'minute',
])]
class MatchStatistic extends Model
{
    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
