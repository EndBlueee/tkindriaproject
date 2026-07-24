<?php

namespace Database\Seeders;

use App\Models\Gelombang;
use App\Models\RincianBiaya;
use App\Models\TahunAjaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Tahun Ajaran Aktif ────────────────────────────────────────
        $tahunAjaran = TahunAjaran::create([
            'nama'                   => '2026/2027',
            'status'                 => 'aktif',
            'maks_termin'            => 4,
        ]);
 
        // ── Rincian Biaya DULU (total: Rp 2.500.000) ─────────────────
        // Harus dibuat sebelum gelombang karena totalBiaya() di model
        // Gelombang menghitung dari rincian biaya
        $rincian = [
            [
                'nama_komponen' => 'Uang Pangkal',
                'nominal'       => 1500000,
                'keterangan'    => 'Dibayar sekali saat pertama masuk',
                'urutan'        => 1,
            ],
            [
                'nama_komponen' => 'Seragam',
                'nominal'       => 500000,
                'keterangan'    => 'Seragam batik dan olahraga',
                'urutan'        => 2,
            ],
            [
                'nama_komponen' => 'SPP Bulan Pertama',
                'nominal'       => 200000,
                'keterangan'    => 'SPP bulan Juli 2026',
                'urutan'        => 4,
            ],
        ];
 
        foreach ($rincian as $r) {
            RincianBiaya::create(array_merge($r, [
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]));
        }
 
        // ── Gelombang (setelah rincian biaya) ─────────────────────────
        // Gelombang 1 diskon 10% → bayar Rp 2.250.000
        // Gelombang 2 diskon 0%  → bayar Rp 2.500.000
        // Gelombang 3 diskon 0%  → bayar Rp 2.500.000
        $gelombang = [
            [
                'nama'          => 'Gelombang 1',
                'tanggal_buka'  => '2026-01-01',
                'tanggal_tutup' => '2026-03-31',
                'batas_pembayaran' => '2026-04-07', // Batas bayar 7 hari setelah gelombang tutup
                'diskon_persen' => 10, // early bird
                'urutan'        => 1,
            ],
            [
                'nama'          => 'Gelombang 2',
                'tanggal_buka'  => '2026-04-01',
                'tanggal_tutup' => '2026-06-30',
                'batas_pembayaran' => '2026-07-07',
                'diskon_persen' => 0,
                'urutan'        => 2,
            ],
            [
                'nama'          => 'Gelombang 3',
                'tanggal_buka'  => '2026-07-01',
                'tanggal_tutup' => '2026-08-31',
                'batas_pembayaran' => '2026-09-07',
                'diskon_persen' => 0,
                'urutan'        => 3,
            ],
        ];
 
        foreach ($gelombang as $g) {
            Gelombang::create(array_merge($g, [
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]));
        }
    }
}
