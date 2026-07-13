<?php

namespace App\Livewire\Admin;

use App\Models\PosTransaction;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Laporan Keuangan')]
class Report extends Component
{
    /** Filter transaksi berdasarkan tipe: null = semua, 'registrasi'|'retail'|'denda' */
    #[Url(as: 'tipe', except: '')]
    public ?string $filterTipe = null;

    /**
     * Set filter tipe transaksi. Jika tipe sama, toggle off (reset ke semua).
     */
    public function filterByTipe(string $tipe): void
    {
        $this->filterTipe = ($this->filterTipe === $tipe) ? null : $tipe;
        unset($this->transactions, $this->revenueChartData);
    }

    /**
     * Reset filter ke semua transaksi.
     */
    public function resetFilter(): void
    {
        $this->filterTipe = null;
        unset($this->transactions, $this->revenueChartData);
    }

    /**
     * Daftar transaksi yang difilter berdasarkan $filterTipe, terbaru di atas.
     *
     * @return Collection<int, PosTransaction>
     */
    #[Computed]
    public function transactions()
    {
        return PosTransaction::with('team:id,name')
            ->when(
                $this->filterTipe,
                fn ($q) => $q->where('transaction_type', $this->filterTipe),
            )
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Data grafik pendapatan per hari per tipe untuk Chart.js.
     *
     * @return array{labels: list<string>, datasets: array{registrasi: list<float>, retail: list<float>, denda: list<float>}}
     */
    #[Computed]
    public function revenueChartData(): array
    {
        $rows = PosTransaction::query()
            ->when(
                $this->filterTipe,
                fn ($q) => $q->where('transaction_type', $this->filterTipe),
            )
            ->selectRaw('DATE(created_at) as date, transaction_type, SUM(total_amount) as total')
            ->groupBy('date', 'transaction_type')
            ->orderBy('date')
            ->get();

        if ($rows->isEmpty()) {
            return ['labels' => [], 'datasets' => ['registrasi' => [], 'retail' => [], 'denda' => []]];
        }

        $dates = $rows->pluck('date')->unique()->sort()->values()->toArray();

        $lookup = [];
        foreach ($rows as $row) {
            $lookup[$row->date][$row->transaction_type] = (float) $row->total;
        }

        $types = ['registrasi', 'retail', 'denda'];
        $datasets = [];
        foreach ($types as $type) {
            $datasets[$type] = array_map(
                fn ($date) => $lookup[$date][$type] ?? 0,
                $dates,
            );
        }

        return ['labels' => $dates, 'datasets' => $datasets];
    }

    public function render()
    {
        $totalRegistrasi = PosTransaction::where('transaction_type', 'registrasi')->sum('total_amount');
        $totalRetail = PosTransaction::where('transaction_type', 'retail')->sum('total_amount');
        $totalDenda = PosTransaction::where('transaction_type', 'denda')->sum('total_amount');
        $totalKeseluruhan = $totalRegistrasi + $totalRetail + $totalDenda;

        return view('livewire.admin.report', compact(
            'totalRegistrasi',
            'totalRetail',
            'totalDenda',
            'totalKeseluruhan',
        ));
    }
}
