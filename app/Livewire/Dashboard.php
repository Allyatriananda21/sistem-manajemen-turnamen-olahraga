<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.dashboard');
    }
}
