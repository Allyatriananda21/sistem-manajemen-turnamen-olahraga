<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public-facing match resource.
 *
 * Includes team names via eager-loaded relationships.
 * Intentionally omits the `notes` column which may contain
 * internal sanction/penalty details not meant for public display.
 */
class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'round' => $this->round,
            'status' => $this->status,
            'match_date' => $this->match_date?->toIso8601String(),
            'venue' => $this->venue,
            'team1' => [
                'id' => $this->team1_id,
                'name' => $this->whenLoaded('team1', fn () => $this->team1->name),
            ],
            'team2' => [
                'id' => $this->team2_id,
                'name' => $this->whenLoaded('team2', fn () => $this->team2->name),
            ],
            'score' => [
                'team1' => $this->score_team1,
                'team2' => $this->score_team2,
            ],
            'winner_id' => $this->winner_id,
        ];
    }
}
