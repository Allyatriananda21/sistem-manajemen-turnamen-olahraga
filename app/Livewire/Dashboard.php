<?php

namespace App\Livewire;

use App\Models\GameMatch;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * Total match count across all statuses.
     */
    #[Computed]
    public function totalMatches(): int
    {
        return GameMatch::count();
    }

    /**
     * Match counts broken down by status.
     *
     * @return array{scheduled: int, ongoing: int, done: int, cancelled: int}
     */
    #[Computed]
    public function matchStatusBreakdown(): array
    {
        $counts = GameMatch::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'scheduled' => $counts['scheduled'] ?? 0,
            'ongoing' => $counts['ongoing'] ?? 0,
            'done' => $counts['done'] ?? 0,
            'cancelled' => $counts['cancelled'] ?? 0,
        ];
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.dashboard');
    }
}
