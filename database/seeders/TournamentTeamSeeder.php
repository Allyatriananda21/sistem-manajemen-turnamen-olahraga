<?php

namespace Database\Seeders;

use App\Models\TournamentTeam;
use Illuminate\Database\Seeder;

class TournamentTeamSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, sport_type: string, coach_name: string, contact_person: string, phone: string, status: string, payment_status: string, invoice_number: string|null}>
     */
    private array $teams = [
        [
            'name' => 'Garuda FC',
            'sport_type' => 'Futsal',
            'coach_name' => 'Budi Santoso',
            'contact_person' => 'Ahmad Rizki',
            'phone' => '081234567801',
            'status' => 'approved',
            'payment_status' => 'paid',
            'invoice_number' => 'INV-2026-001',
        ],
        [
            'name' => 'Elang United',
            'sport_type' => 'Futsal',
            'coach_name' => 'Hendra Wijaya',
            'contact_person' => 'Doni Pratama',
            'phone' => '081234567802',
            'status' => 'approved',
            'payment_status' => 'paid',
            'invoice_number' => 'INV-2026-002',
        ],
        [
            'name' => 'Macan Putih',
            'sport_type' => 'Futsal',
            'coach_name' => 'Slamet Riyadi',
            'contact_person' => 'Faisal Amin',
            'phone' => '081234567803',
            'status' => 'approved',
            'payment_status' => 'unpaid',
            'invoice_number' => 'INV-2026-003',
        ],
        [
            'name' => 'Rajawali SC',
            'sport_type' => 'Futsal',
            'coach_name' => 'Teguh Prasetyo',
            'contact_person' => 'Rudi Hartono',
            'phone' => '081234567804',
            'status' => 'approved',
            'payment_status' => 'paid',
            'invoice_number' => 'INV-2026-004',
        ],
        [
            'name' => 'Banteng Muda',
            'sport_type' => 'Futsal',
            'coach_name' => 'Agus Setiawan',
            'contact_person' => 'Wahyu Nugroho',
            'phone' => '081234567805',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'invoice_number' => null,
        ],
        [
            'name' => 'Singa Barat',
            'sport_type' => 'Futsal',
            'coach_name' => 'Eko Purnomo',
            'contact_person' => 'Dedy Kusuma',
            'phone' => '081234567806',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'invoice_number' => null,
        ],
        [
            'name' => 'Harimau FC',
            'sport_type' => 'Futsal',
            'coach_name' => 'Bambang Suprapto',
            'contact_person' => 'Irwan Susanto',
            'phone' => '081234567807',
            'status' => 'disqualified',
            'payment_status' => 'unpaid',
            'invoice_number' => 'INV-2026-007',
        ],
        [
            'name' => 'Naga Api',
            'sport_type' => 'Futsal',
            'coach_name' => 'Yudi Hermawan',
            'contact_person' => 'Sigit Prabowo',
            'phone' => '081234567808',
            'status' => 'approved',
            'payment_status' => 'paid',
            'invoice_number' => 'INV-2026-008',
        ],
    ];

    public function run(): void
    {
        foreach ($this->teams as $team) {
            TournamentTeam::create($team);
        }
    }
}
