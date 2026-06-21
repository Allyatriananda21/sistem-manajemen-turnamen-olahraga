<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public-facing team resource.
 *
 * Intentionally omits private contact fields (phone, contact_person, coach_name)
 * that are only relevant to internal admin operations.
 */
class TeamResource extends JsonResource
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
            'name' => $this->name,
            'sport_type' => $this->sport_type,
            'logo' => $this->logo ? asset('storage/'.$this->logo) : null,
            'payment_status' => $this->payment_status,
        ];
    }
}
