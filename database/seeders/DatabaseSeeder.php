<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Bersihkan direktori storage agar tidak ada file nyangkut dari testing sebelumnya
        Storage::disk('public')->deleteDirectory('temp');
        Storage::disk('public')->deleteDirectory('dokumen');

        $this->call([
           UserSeeder::class,
           TahunAjaranSeeder::class,
           PersyaratanDokumenSeeder::class,
        ]);
    }
}
