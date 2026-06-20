<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $sport_type
 * @property string|null $coach_name
 * @property string $contact_person
 * @property string $phone
 * @property string|null $logo
 * @property Carbon $registered_at
 * @property string $status
 * @property string $payment_status
 * @property string|null $invoice_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'sport_type',
    'coach_name',
    'contact_person',
    'phone',
    'logo',
    'registered_at',
    'status',
    'payment_status',
    'invoice_number',
])]
class TournamentTeam extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Get the matches where this team is team 1.
     */
    public function matchesAsTeam1(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'team1_id');
    }

    /**
     * Get the matches where this team is team 2.
     */
    public function matchesAsTeam2(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'team2_id');
    }

    /**
     * Generate a unique invoice number for this team.
     *
     * Format: INV-{year}-{id padded to 4 digits}
     * Example: INV-2026-0007
     *
     * Only call this when invoice_number is empty (first approval).
     */
    public function generateInvoiceNumber(): string
    {
        return sprintf('INV-%d-%04d', now()->year, $this->id);
    }
}
