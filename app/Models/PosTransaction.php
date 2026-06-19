<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTransaction extends Model
{
    protected $fillable = [
        'transaction_type',
        'team_id',
        'total_amount',
        'payment_method',
        'cashier_name',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PosTransactionDetail::class);
    }
}
