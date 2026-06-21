<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StandingResource;
use App\Models\Standing;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicStandingController extends Controller
{
    /**
     * Return the current standings for the public landing page.
     *
     * GET /api/public/standings
     *
     * Ordered by points DESC, then goal_diff DESC (standard tiebreaker),
     * then win DESC as a secondary tiebreaker.
     *
     * No authentication required — read-only public endpoint.
     */
    public function index(): AnonymousResourceCollection
    {
        $standings = Standing::with('team:id,name')
            ->orderByDesc('points')
            ->orderByDesc('goal_diff')
            ->orderByDesc('win')
            ->get();

        return StandingResource::collection($standings);
    }
}
