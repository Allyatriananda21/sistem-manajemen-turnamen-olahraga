<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use App\Models\MatchStatistic;
use App\Models\Player;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Statistik Pertandingan')]
class MatchStatisticsInput extends Component
{
    public GameMatch $match;

    // Form fields
    public string $formPlayerId = '';

    public string $formStatType = 'goal';

    public string $formMinute = '';

    public static array $statLabels = [
        'goal' => '⚽ Gol',
        'assist' => '🅰️ Assist',
        'yellow_card' => '🟨 Kartu Kuning',
        'red_card' => '🟥 Kartu Merah',
        'mvp' => '⭐ MVP',
    ];

    public function mount(GameMatch $match): void
    {
        abort_unless($match->status === 'done', 403, 'Statistik hanya bisa diinput setelah pertandingan selesai.');

        $this->match = $match->load(['team1', 'team2']);
    }

    /**
     * All players from both teams, for the player dropdown.
     *
     * @return Collection<int, Player>
     */
    #[Computed]
    public function players(): Collection
    {
        return Player::whereIn('team_id', [$this->match->team1_id, $this->match->team2_id])
            ->orderBy('team_id')
            ->orderBy('jersey_number')
            ->orderBy('full_name')
            ->with('team:id,name')
            ->get();
    }

    /**
     * Existing statistics for this match, newest first.
     *
     * @return Collection<int, MatchStatistic>
     */
    #[Computed]
    public function statistics(): Collection
    {
        return MatchStatistic::where('match_id', $this->match->id)
            ->with('player.team:id,name')
            ->orderByDesc('id')
            ->get();
    }

    public function addStat(): void
    {
        $validated = $this->validate([
            'formPlayerId' => ['required', 'integer', 'exists:players,id'],
            'formStatType' => ['required', 'in:goal,assist,yellow_card,red_card,mvp'],
            'formMinute' => ['nullable', 'integer', 'min:1', 'max:200'],
        ], [
            'formPlayerId.required' => 'Pilih pemain terlebih dahulu.',
            'formPlayerId.exists' => 'Pemain tidak valid.',
            'formStatType.required' => 'Pilih tipe statistik.',
            'formMinute.integer' => 'Menit harus berupa angka.',
            'formMinute.min' => 'Menit minimal 1.',
            'formMinute.max' => 'Menit maksimal 200.',
        ]);

        // Validate player belongs to one of the two teams
        $player = Player::find($validated['formPlayerId']);
        abort_unless(
            in_array($player->team_id, [$this->match->team1_id, $this->match->team2_id], strict: true),
            403
        );

        MatchStatistic::create([
            'match_id' => $this->match->id,
            'player_id' => $validated['formPlayerId'],
            'stat_type' => $validated['formStatType'],
            'minute' => $validated['formMinute'] ?: null,
        ]);

        $this->formPlayerId = '';
        $this->formMinute = '';
        unset($this->statistics);

        Flux::toast(variant: 'success', text: 'Statistik berhasil ditambahkan.');
    }

    public function deleteStat(int $statId): void
    {
        $stat = MatchStatistic::where('match_id', $this->match->id)->findOrFail($statId);
        $stat->delete();

        unset($this->statistics);
        Flux::toast(variant: 'danger', text: 'Statistik berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.match-statistics-input');
    }
}
