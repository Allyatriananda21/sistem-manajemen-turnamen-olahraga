<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class PublicMatchController extends Controller
{
    /**
     * Return matches for the public landing page.
     *
     * GET /api/public/matches
     *
     * Optional query parameters:
     *   ?status=scheduled|ongoing|done|cancelled
     *   ?round=  (exact round name, e.g. "Semifinal")
     *
     * No authentication required — read-only public endpoint.
     * The `notes` field is excluded via MatchResource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'status' => ['nullable', Rule::in(['scheduled', 'ongoing', 'done', 'cancelled'])],
            'round' => ['nullable', 'string', 'max:50'],
        ]);

        $matches = GameMatch::with(['team1:id,name', 'team2:id,name', 'winner:id,name'])
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->input('status'))
            )
            ->when(
                $request->filled('round'),
                fn ($q) => $q->where('round', $request->input('round'))
            )
            ->orderBy('match_date')
            ->orderBy('id')
            ->get();

        return MatchResource::collection($matches);
    }
}
