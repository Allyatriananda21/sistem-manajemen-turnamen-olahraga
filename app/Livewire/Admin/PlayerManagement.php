<?php

namespace App\Livewire\Admin;

use App\Models\Player;
use App\Models\TournamentTeam;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pemain Tim')]
class PlayerManagement extends Component
{
    use WithPagination;

    public TournamentTeam $team;

    // Form fields
    public string $formFullName = '';

    public string $formJerseyNumber = '';

    public string $formPosition = '';

    // Modal state
    public bool $showFormModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingPlayerId = null;

    public ?int $deletingPlayerId = null;

    public string $deletingPlayerName = '';

    public function mount(TournamentTeam $team): void
    {
        $this->team = $team;
    }

    // -----------------------------------------------------------------------
    // Create / Edit
    // -----------------------------------------------------------------------

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEdit(int $playerId): void
    {
        $player = Player::findOrFail($playerId);

        $this->editingPlayerId = $playerId;
        $this->formFullName = $player->full_name;
        $this->formJerseyNumber = (string) ($player->jersey_number ?? '');
        $this->formPosition = $player->position ?? '';

        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formFullName' => ['required', 'string', 'max:100'],
            'formJerseyNumber' => ['nullable', 'integer', 'min:1', 'max:99'],
            'formPosition' => ['nullable', 'string', 'max:50'],
        ], [
            'formFullName.required' => 'Nama pemain wajib diisi.',
            'formJerseyNumber.integer' => 'Nomor punggung harus angka.',
            'formJerseyNumber.min' => 'Nomor punggung minimal 1.',
            'formJerseyNumber.max' => 'Nomor punggung maksimal 99.',
        ]);

        $data = [
            'team_id' => $this->team->id,
            'full_name' => $validated['formFullName'],
            'jersey_number' => $validated['formJerseyNumber'] ?: null,
            'position' => $validated['formPosition'] ?: null,
        ];

        if ($this->editingPlayerId) {
            Player::findOrFail($this->editingPlayerId)->update($data);
            $message = "Pemain \"{$validated['formFullName']}\" berhasil diperbarui.";
        } else {
            Player::create($data);
            $message = "Pemain \"{$validated['formFullName']}\" berhasil ditambahkan.";
        }

        $this->showFormModal = false;
        $this->resetForm();
        Flux::toast(variant: 'success', text: $message);
    }

    // -----------------------------------------------------------------------
    // Delete
    // -----------------------------------------------------------------------

    public function openDelete(int $playerId): void
    {
        $player = Player::findOrFail($playerId);

        $this->deletingPlayerId = $playerId;
        $this->deletingPlayerName = $player->full_name;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        $player = Player::findOrFail($this->deletingPlayerId);
        $name = $player->full_name;

        $player->delete();

        $this->showDeleteModal = false;
        $this->deletingPlayerId = null;
        Flux::toast(variant: 'danger', text: "Pemain \"{$name}\" berhasil dihapus.");
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render()
    {
        $players = Player::where('team_id', $this->team->id)
            ->orderBy('jersey_number')
            ->orderBy('full_name')
            ->paginate(20);

        return view('livewire.admin.player-management', compact('players'));
    }

    private function resetForm(): void
    {
        $this->formFullName = '';
        $this->formJerseyNumber = '';
        $this->formPosition = '';
        $this->editingPlayerId = null;
        $this->resetValidation();
    }
}
