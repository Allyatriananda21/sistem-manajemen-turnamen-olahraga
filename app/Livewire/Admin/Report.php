<?php

namespace App\Livewire\Admin;

use App\Models\PosTransaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Laporan Keuangan')]
class Report extends Component
{
    public function render()
    {
        $totalRegistrasi = PosTransaction::where('transaction_type', 'registrasi')->sum('total_amount');
        $totalRetail = PosTransaction::where('transaction_type', 'retail')->sum('total_amount');
        $totalDenda = PosTransaction::where('transaction_type', 'denda')->sum('total_amount');
        $totalKeseluruhan = $totalRegistrasi + $totalRetail + $totalDenda;

        $recentTransactions = PosTransaction::with('team:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('livewire.admin.report', compact(
            'totalRegistrasi',
            'totalRetail',
            'totalDenda',
            'totalKeseluruhan',
            'recentTransactions',
        ));
    }
}
