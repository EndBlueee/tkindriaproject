<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\PersyaratanDokumen;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class PersyaratanDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::aktif();
 
        if (! $tahunAjaran) {
            $this->command->warn('Tidak ada tahun ajaran aktif. Jalankan TahunAjaranSeeder dulu.');
            return;
        }
 
        $persyaratans = [
            [
                'nama_dokumen'   => 'Akta Lahir',
                'format_file'    => 'pdf,jpg,png',
                'maks_ukuran_kb' => 2048,
                'is_wajib'       => true,
                'keterangan'     => 'Scan atau foto akta lahir anak yang jelas',
                'urutan'         => 1,
            ],
            [
                'nama_dokumen'   => 'Kartu Keluarga',
                'format_file'    => 'pdf,jpg,png',
                'maks_ukuran_kb' => 2048,
                'is_wajib'       => true,
                'keterangan'     => 'Scan atau foto KK yang masih berlaku',
                'urutan'         => 2,
            ],
            [
                'nama_dokumen'   => 'Foto Anak 3x4',
                'format_file'    => 'jpg,png',
                'maks_ukuran_kb' => 1024,
                'is_wajib'       => true,
                'keterangan'     => 'Foto terbaru, latar belakang merah',
                'urutan'         => 3,
            ],
            [
                'nama_dokumen'   => 'KTP Orang Tua / Wali',
                'format_file'    => 'pdf,jpg,png',
                'maks_ukuran_kb' => 2048,
                'is_wajib'       => true,
                'keterangan'     => 'KTP ayah atau ibu yang masih berlaku',
                'urutan'         => 4,
            ],
            
        ];
 
        foreach ($persyaratans as $p) {
            PersyaratanDokumen::create(array_merge($p, [
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]));
        }
    }
}
