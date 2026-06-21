<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\TournamentTeam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicTeamController extends Controller
{
    /**
     * Return all approved teams for the public landing page.
     *
     * GET /api/public/teams
     *
     * No authentication required — read-only public endpoint.
     * Private contact information is excluded via TeamResource.
     */
    public function index(): AnonymousResourceCollection
    {
        $teams = TournamentTeam::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return TeamResource::collection($teams);
    }

    /**
     * Register a new team from the public landing page.
     *
     * POST /api/public/teams/register
     *
     * No authentication required — public registration endpoint.
     * Status defaults to 'pending' and payment_status to 'unpaid'.
     * Logo is stored at storage/app/public/team-logos if provided.
     */
    public function store(RegisterTeamRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('team-logos', 'public');
        }

        $team = TournamentTeam::create([
            'name' => $validated['name'],
            'sport_type' => $validated['sport_type'],
            'coach_name' => $validated['coach_name'] ?? null,
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'logo' => $logoPath,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return (new TeamResource($team))
            ->response()
            ->setStatusCode(201);
    }
}
