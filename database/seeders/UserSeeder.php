<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Akun Admin (Bunda Ning) ───────────────────────────────────
        User::create([
            'nama'     => 'Bunda Ning',
            'nomor_hp' => '08122222222',
            'password' => 'admin123',   // otomatis di-hash karena cast 'hashed'
            'role'     => 'admin',
            'is_aktif' => true,
        ]);

        // ── Akun Orang Tua Dummy (untuk testing) ─────────────────────
        $orangTuas = [
            [
                'nama'     => 'Budi Santoso',
                'nomor_hp' => '08133333333',
                'password' => 'ortu123',
            ],
            [
                'nama'     => 'Siti Rahayu',
                'nomor_hp' => '08144444444',
                'password' => 'ortu123',
            ],
            [
                'nama'     => 'Ahmad Fauzi',
                'nomor_hp' => '08155555555',
                'password' => 'ortu123',
            ],
        ];

        foreach ($orangTuas as $ortu) {
            User::create([
                'nama'     => $ortu['nama'],
                'nomor_hp' => $ortu['nomor_hp'],
                'password' => $ortu['password'],
                'role'     => 'orangtua',
                'is_aktif' => true,
            ]);
        }
    }
}