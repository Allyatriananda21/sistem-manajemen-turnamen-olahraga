<?php

namespace App\Livewire\Admin;

use App\Models\TournamentTeam;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Daftar Tim Peserta')]
class TeamList extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var string 'all'|'pending'|'approved'|'disqualified' */
    public string $statusFilter = 'all';

    // Confirmation modal state
    public bool $showConfirmModal = false;

    public ?int $confirmTeamId = null;

    public string $confirmAction = ''; // 'approve' | 'disqualify'

    public string $confirmTeamName = '';

    /**
     * Reset pagination when search or filter changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open confirmation modal for the given team and action.
     */
    public function openConfirmModal(int $teamId, string $action): void
    {
        $team = TournamentTeam::findOrFail($teamId);

        $this->confirmTeamId = $teamId;
        $this->confirmAction = $action;
        $this->confirmTeamName = $team->name;
        $this->showConfirmModal = true;
    }

    /**
     * Execute the confirmed action.
     */
    public function executeAction(): void
    {
        if (! $this->confirmTeamId || ! in_array($this->confirmAction, ['approve', 'disqualify'], strict: true)) {
            $this->resetConfirm();

            return;
        }

        $team = TournamentTeam::findOrFail($this->confirmTeamId);

        $newStatus = $this->confirmAction === 'approve' ? 'approved' : 'disqualified';

        $updateData = ['status' => $newStatus];

        // Generate invoice only on first approval — preserve existing invoice_number
        if ($newStatus === 'approved' && empty($team->invoice_number)) {
            $updateData['invoice_number'] = $team->generateInvoiceNumber();
        }

        $team->update($updateData);

        $label = $newStatus === 'approved' ? 'disetujui' : 'didiskualifikasi';
        Flux::toast(variant: 'success', text: "Tim {$team->name} berhasil {$label}.");

        $this->resetConfirm();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $teams = TournamentTeam::query()
            ->when(
                $this->search,
                fn ($query) => $query->where('name', 'like', "%{$this->search}%")
            )
            ->when(
                $this->statusFilter !== 'all',
                fn ($query) => $query->where('status', $this->statusFilter)
            )
            ->orderBy('registered_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.team-list', compact('teams'));
    }

    /**
     * Reset confirmation modal state.
     */
    private function resetConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmTeamId = null;
        $this->confirmAction = '';
        $this->confirmTeamName = '';
    }
}
