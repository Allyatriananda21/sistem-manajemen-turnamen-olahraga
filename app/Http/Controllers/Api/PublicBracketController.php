<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use Illuminate\Http\JsonResponse;

class PublicBracketController extends Controller
{
    /**
     * Round ordering for known knockout stage names (early → final).
     * Rounds not listed here are sorted alphabetically after known ones.
     *
     * @var array<string, int>
     */
    private const ROUND_ORDER = [
        '32 Besar' => 10,
        '16 Besar' => 20,
        'Perempat Final' => 30,
        'Semifinal' => 40,
        'Final' => 50,
    ];

    /**
     * Return knockout bracket data grouped by round for the public landing page.
     *
     * GET /api/public/bracket
     *
     * Only includes matches whose round is NOT a "Matchday X" pattern
     * (i.e. Round-Robin matches are excluded — knockout rounds only).
     *
     * Response shape:
     * {
     *   "data": [
     *     {
     *       "round": "Perempat Final",
     *       "matches": [ { ...MatchResource fields... } ]
     *     },
     *     ...
     *   ]
     * }
     *
     * No authentication required — read-only public endpoint.
     */
    public function index(): JsonResponse
    {
        $matches = GameMatch::with(['team1:id,name', 'team2:id,name', 'winner:id,name'])
            ->whereNotNull('round')
            // Exclude Round-Robin matchdays: "Matchday 1", "Matchday 2", etc.
            ->where('round', 'not like', 'Matchday%')
            ->orderBy('match_date')
            ->orderBy('id')
            ->get();

        // Group by round, then sort groups by known bracket order
        $grouped = $matches
            ->groupBy('round')
            ->sortBy(fn ($_, $round) => self::ROUND_ORDER[$round] ?? 99)
            ->map(fn ($roundMatches, $round) => [
                'round' => $round,
                'matches' => MatchResource::collection($roundMatches)->resolve(),
            ])
            ->values();

        return response()->json(['data' => $grouped]);
    }
}
