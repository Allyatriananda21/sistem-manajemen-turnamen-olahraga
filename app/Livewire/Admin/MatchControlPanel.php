<?php

namespace App\Livewire\Admin;

use App\Models\GameMatch;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Kontrol Pertandingan')]
class MatchControlPanel extends Component
{
    public GameMatch $match;

    // Score inputs — live preview, committed only on saveScore()
    public int $scoreTeam1 = 0;

    public int $scoreTeam2 = 0;

    // Notes — editable when status is not 'scheduled'
    public string $notes = '';

    // Confirmation modal for finishing with 0-0 score
    public bool $showZeroScoreConfirm = false;

    /**
     * Valid linear status progression.
     *
     * @var array<string, string>
     */
    private const STATUS_NEXT = [
        'scheduled' => 'ongoing',
        'ongoing' => 'done',
    ];

    public function mount(GameMatch $match): void
    {
        $this->match = $match->load(['team1', 'team2']);

        $this->scoreTeam1 = $match->score_team1;
        $this->scoreTeam2 = $match->score_team2;
        $this->notes = $match->notes ?? '';
    }

    /**
     * Advance status along the linear progression:
     * scheduled → ongoing → done.
     *
     * Jumping from scheduled directly to done is not allowed.
     * When advancing to 'done', delegates to finalizeToDone().
     */
    public function advance(): void
    {
        $current = $this->match->status;

        if (! array_key_exists($current, self::STATUS_NEXT)) {
            Flux::toast(
                variant: 'warning',
                text: 'Status pertandingan tidak dapat dilanjutkan dari kondisi saat ini.'
            );

            return;
        }

        $next = self::STATUS_NEXT[$current];

        if ($next === 'done') {
            // Re-read latest scores from DB to avoid stale Livewire property values
            $this->match->refresh();

            // Prompt confirmation if both scores are still 0
            if ($this->match->score_team1 === 0 && $this->match->score_team2 === 0) {
                $this->showZeroScoreConfirm = true;

                return;
            }

            $this->finalizeToDone();

            return;
        }

        $this->match->update(['status' => $next]);
        $this->match->refresh();

        Flux::toast(variant: 'success', text: 'Status pertandingan berhasil diubah ke dimulai (Ongoing).');
    }

    /**
     * Called when user explicitly confirms finishing a 0-0 match.
     */
    public function confirmFinishZeroScore(): void
    {
        $this->showZeroScoreConfirm = false;
        $this->finalizeToDone();
    }

    /**
     * Set status to 'done' and determine winner_id from current DB scores.
     *
     * - team1 score > team2 score → winner_id = team1_id
     * - team2 score > team1 score → winner_id = team2_id
     * - draw                      → winner_id = null
     */
    private function finalizeToDone(): void
    {
        $s1 = $this->match->score_team1;
        $s2 = $this->match->score_team2;

        $winnerId = match (true) {
            $s1 > $s2 => $this->match->team1_id,
            $s2 > $s1 => $this->match->team2_id,
            default => null, // draw
        };

        $this->match->update([
            'status' => 'done',
            'winner_id' => $winnerId,
        ]);

        $this->match->refresh();

        $resultNote = match (true) {
            $winnerId === $this->match->team1_id => "Pemenang: {$this->match->team1->name}",
            $winnerId === $this->match->team2_id => "Pemenang: {$this->match->team2->name}",
            default => 'Hasil Seri',
        };

        Flux::toast(variant: 'success', text: "Pertandingan selesai. {$resultNote}.");
    }

    /**
     * Cancel the match from any status except 'done'.
     */
    public function cancel(): void
    {
        if ($this->match->status === 'done') {
            Flux::toast(
                variant: 'warning',
                text: 'Pertandingan yang sudah selesai tidak dapat dibatalkan.'
            );

            return;
        }

        $this->match->update(['status' => 'cancelled']);
        $this->match->refresh();

        Flux::toast(variant: 'danger', text: 'Pertandingan telah dibatalkan.');
    }

    /**
     * Persist score_team1 and score_team2 to the database.
     * Only allowed while match is 'ongoing'.
     */
    public function saveScore(): void
    {
        if ($this->match->status !== 'ongoing') {
            Flux::toast(variant: 'warning', text: 'Skor hanya bisa disimpan saat pertandingan berlangsung.');

            return;
        }

        $validated = $this->validate([
            'scoreTeam1' => ['required', 'integer', 'min:0'],
            'scoreTeam2' => ['required', 'integer', 'min:0'],
        ], [
            'scoreTeam1.min' => 'Skor tidak boleh negatif.',
            'scoreTeam2.min' => 'Skor tidak boleh negatif.',
        ]);

        $this->match->update([
            'score_team1' => $validated['scoreTeam1'],
            'score_team2' => $validated['scoreTeam2'],
        ]);

        $this->match->refresh();

        Flux::toast(variant: 'success', text: 'Skor berhasil disimpan.');
    }

    /**
     * Persist notes/catatan sanksi to the database.
     * Allowed for any status except 'scheduled'.
     */
    public function saveNotes(): void
    {
        if ($this->match->status === 'scheduled') {
            Flux::toast(variant: 'warning', text: 'Catatan hanya bisa ditambahkan setelah pertandingan dimulai.');

            return;
        }

        $validated = $this->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'notes.max' => 'Catatan maksimal 2000 karakter.',
        ]);

        $this->match->update(['notes' => $validated['notes'] ?: null]);
        $this->match->refresh();

        Flux::toast(variant: 'success', text: 'Catatan berhasil disimpan.');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.match-control-panel');
    }
}
