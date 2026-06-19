<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, email: string, password: string, role: string, is_active: bool}>
     */
    private array $users = [
        [
            'name' => 'Admin Panitia',
            'email' => 'admin@turnamen.test',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ],
        [
            'name' => 'Wasit Lapangan',
            'email' => 'wasit@turnamen.test',
            'password' => 'password',
            'role' => 'wasit',
            'is_active' => true,
        ],
        [
            'name' => 'Kasir Event',
            'email' => 'kasir@turnamen.test',
            'password' => 'password',
            'role' => 'kasir',
            'is_active' => true,
        ],
    ];

    public function run(): void
    {
        foreach ($this->users as $userData) {
            User::create([
                ...$userData,
                'password' => Hash::make($userData['password']),
            ]);
        }
    }
}
