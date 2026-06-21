<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public-facing standings resource.
 *
 * Includes team name via the eager-loaded team relationship.
 * The `rank` field is computed from the collection index in StandingResource::collection()
 * and injected via the `additional` context when building the response.
 */
class StandingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team_id'   => $this->team_id,
            'team_name' => $this->whenLoaded('team', fn () => $this->team->name),
            'played'    => $this->played,
            'win'       => $this->win,
            'draw'      => $this->draw,
            'lose'      => $this->lose,
            'points'    => $this->points,
            'goal_diff' => $this->goal_diff,
        ];
    }
}
