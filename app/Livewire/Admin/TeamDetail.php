<?php

namespace App\Livewire\Admin;

use App\Models\TournamentTeam;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Detail Tim')]
class TeamDetail extends Component
{
    public TournamentTeam $team;

    /**
     * Mount the component with route model binding.
     */
    public function mount(TournamentTeam $team): void
    {
        $this->team = $team;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.team-detail');
    }
}
